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
    private $pickup;
    private $dropoffs;
    private $config;
    private $graphHopperClient;

    /**
     * GraphHopper constructor.
     */
    public function __construct($pickup, $dropoffs, $config, $client = null)
    {
        $this->client = $client === null ? new Client() : $client;
        $this->pickup = $pickup;
        $this->dropoffs = $dropoffs;
        $this->config = $config;

        $this->graphHopperClient = new \Stuart\Routing\GraphHopper\Client($this->client, $this->config['graphhopper_api_key']);

        $errors = $this->validate();
        if (count($errors) > 0) {
            throw new ClientException($errors);
        }
    }

    /**
     * Experimental feature allowing you to group your orders before sending multi-drop jobs
     * to the Stuart API.
     * You are going to need a GraphHoppper API key in order to start using their service.
     * This method is the only one you need to use, it returns you the already routed Jobs,
     * and also the waste (orders that hasn't been dispatched).
     *
     * @return object
     */
    public function findRounds()
    {
        $optimizedApiResponse = $this->graphHopperClient->optimize($this->pickup, $this->dropoffs, $this->config);
        if (!$optimizedApiResponse->success()) {
            error_log('Unable to send request to GraphHopper.');
            return (object)array(
                'jobs' => [],
                'waste' => []
            );
        }

        $solutionApiResponse = $this->pollForFinishedSolution(json_decode($optimizedApiResponse->getBody())->job_id);
        if (!$solutionApiResponse->success()) {
            error_log('Unable to fetch response from GraphHopper.');
            return (object)array(
                'jobs' => [],
                'waste' => []
            );
        }

        $jobs = array();
        $solution = json_decode($solutionApiResponse->getBody())->solution;
        if (!empty($solution)) {
            foreach ($solution->routes as $route) {
                $sortedDropoffs = $this->sortedDropoffs($this->parseForStops($route));
                if (count($sortedDropoffs) > 0) {
                    $jobs[] = $this->buildJob($route->waiting_time, $sortedDropoffs);
                }
            }
        }

        $waste = array();
        if (!empty($solution)) {
            foreach ($solution->unassigned->services as $address) {
                $waste[] = $this->matchDropoff($address);
            }
        }

        return (object)array(
            'jobs' => $jobs,
            'waste' => $waste
        );
    }

    private function buildJob($waitingTime, $sortedDropoffs)
    {
        $job = new \Stuart\Job();
        $this->clearDropoffAt($sortedDropoffs);

        foreach ($sortedDropoffs as $dropoff) {
            $job->pushDropoff($dropoff);
        }

        $pickupClone = clone $this->pickup;
        $pickupAt = new \DateTime();
        $pickupAt->setTimestamp($waitingTime);
        $pickupClone->setPickupAt($pickupAt);
        $job->pushPickup($pickupClone);

        return $job;
    }

    private function sortedDropoffs($stops)
    {
        $result = array();

        foreach ($stops as $stop) {
            $result[] = clone($this->matchDropoff($stop));
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

    private function matchDropoff($address)
    {
        foreach ($this->dropoffs as $dropoff) {
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
        }

        return $solutionApiResponse;
    }

    private function validate()
    {
        $errors = array();

        if ($this->pickup->getPickupAt() !== null) {
            $errors[] = new Error('PICKUP_AT_MUST_BE_NULL');
        }

        foreach ($this->dropoffs as $dropoff) {
            if ($dropoff->getDropoffAt() === null) {
                $errors[] = new Error('DROPOFF_AT_MUST_BE_SPECIFIED_FOR_EACH_DROPOFF');
            }
        }

        return $errors;
    }
}
