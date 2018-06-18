<?php

namespace Stuart\Routing;

use GuzzleHttp\Client;
use Stuart\ClientException;
use Stuart\Validation\Error;

class GraphHopper
{
    /**
     * @var Client
     */
    private $client;
    private $config;
    private $graphHopperClient;

    /**
     * GraphHopper constructor.
     *
     * Configuration ($config) is important in order to make the integration with graphhopper working correctly.
     * For more details on different plan, please refer to: https://www.graphhopper.com/pricing/
     *
     */
    public function __construct($config, $client = null)
    {
        $this->client = $client === null ? new Client() : $client;
        $this->config = $config;

        $this->graphHopperClient = new \Stuart\Routing\GraphHopper\Client($this->config['graphhopper_api_key'], $this->client);
    }

    /**
     * Experimental feature allowing you to group your orders before sending multi-drop jobs
     * to the Stuart API.
     * You are going to need a GraphHoppper API key in order to start using their service.
     * This method is the only one you need to use, it returns you the already routed Jobs,
     * and also the waste (orders that hasn't been dispatched).
     *
     * @param $pickup
     * @param $dropoffs
     * @return array
     * @throws ClientException
     */

    public function findRounds($pickup, $dropoffs)
    {
        $errors = $this->validate($pickup, $dropoffs);
        if (count($errors) > 0) {
            throw new ClientException(implode($errors));
        }
        return $this->findRoundsRec($pickup, $dropoffs, [], [], 0);
    }

    private function findRoundsRec($pickup, $dropoffs, $jobs, $waste)
    {
        $optimizedApiResponse = $this->graphHopperClient->optimize($pickup, $dropoffs, $this->config);
        if (!$optimizedApiResponse->success()) {
            throw new ClientException('Unable to send request to GraphHopper. Details: ' . $optimizedApiResponse->getBody());
        }

        $solutionApiResponse = $this->pollForFinishedSolution(json_decode($optimizedApiResponse->getBody())->job_id);
        if (!$solutionApiResponse->success()) {
            throw new ClientException('Unable to send request to GraphHopper. Details: ' . $solutionApiResponse->getBody());
        }

        $solution = json_decode($solutionApiResponse->getBody())->solution;
        if (!empty($solution)) {
            foreach ($solution->routes as $route) {
                $sortedDropoffs = $this->sortedDropoffs($dropoffs, $this->parseForStops($route));
                if (count($sortedDropoffs) > 0) {
                    $jobs[] = $this->buildJob($pickup, $route->waiting_time, $sortedDropoffs);
                }
            }
        }

        if (!empty($solution)) {
            foreach ($solution->unassigned->services as $address) {
                $waste[] = $this->matchDropoff($dropoffs, $address);
            }
        }

        if (count($waste) === 0) {
            return $jobs;
        } else if (count($waste) === count($dropoffs)) {
            throw new ClientException('Not able to find round with the given configuration, waste: ' . $waste);
        } else {
            return $this->findRoundsRec($pickup, $waste, $jobs, []);
        }
    }

    private function buildJob($pickup, $waitingTime, $sortedDropoffs)
    {
        $job = new \Stuart\Job();
        $this->clearDropoffAt($sortedDropoffs);

        foreach ($sortedDropoffs as $dropoff) {
            $job->pushDropoff($dropoff);
        }

        $pickupClone = clone $pickup;
        $pickupAt = new \DateTime();
        $pickupAt->setTimestamp($waitingTime);
        $pickupClone->setPickupAt($pickupAt);
        $job->pushPickup($pickupClone);

        return $job;
    }

    private function sortedDropoffs($dropoffs, $stops)
    {
        $result = array();

        foreach ($stops as $stop) {
            $result[] = clone($this->matchDropoff($dropoffs, $stop));
        }

        return $result;
    }

    private function parseForStops($route)
    {
        unset($route->activities[0], $route->activities[1]);

        $addresses = array();
        foreach ($route->activities as $activity) {
            $addresses[] = $activity->address->location_id;
        }

        return $addresses;
    }

    private function matchDropoff($dropoffs, $address)
    {
        foreach ($dropoffs as $dropoff) {
            if ($dropoff->getAddress() === $address) {
                return $dropoff;
            }
        }
    }

    private function clearDropoffAt($dropoffs)
    {
        foreach ($dropoffs as $dropoff) {
            $dropoff->setDropoffAt(null);
        }
    }

    private function pollForFinishedSolution($jobId)
    {
        $solutionApiResponse = $this->graphHopperClient->solution($jobId);
        $solutionStatus = json_decode($solutionApiResponse->getBody())->status;

        while ($solutionStatus !== 'finished') {
            $solutionApiResponse = $this->graphHopperClient->solution($jobId);
            $solutionStatus = json_decode($solutionApiResponse->getBody())->status;
            sleep(1);
        }

        return $solutionApiResponse;
    }

    private function validate($pickup, $dropoffs)
    {
        $errors = array();

        if ($pickup->getPickupAt() !== null) {
            $errors[] = new Error('PICKUP_AT_MUST_BE_NULL');
        }

        foreach ($dropoffs as $dropoff) {
            if ($dropoff->getDropoffAt() === null) {
                $errors[] = new Error('DROPOFF_AT_MUST_BE_SPECIFIED_FOR_EACH_DROPOFF');
            }
        }

        if (count($dropoffs) > $this->config['max_dropoffs']) {
            $errors[] = new Error('TOO_MANY_DROPOFFS');
        }

        return $errors;
    }
}
