<?php

namespace Stuart\Routing;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Stuart\Infrastructure\ApiResponseFactory;

class GraphHopper
{
    const GRAPHHOPPER_API_KEY = 'f8b0585b-1bed-4cda-aede-dfdd2c4899a9';

    /**
     * @var Client
     */
    private $client;

    /**
     * GraphHopper constructor.
     */
    public function __construct()
    {
        $this->client = new Client();
    }

    public function findRounds($pickup, $dropoffs)
    {
        $optimizedApiResponse = $this->httpPostOptimize($pickup, $dropoffs);
        if (!$optimizedApiResponse->success()) {
            // TODO: handle error
            return null;
        }

        $solutionApiResponse = $this->pollForFinishedSolution(json_decode($optimizedApiResponse->getBody())->job_id);
        if (!$solutionApiResponse->success()) {
            // TODO: handle error
            return null;
        }

        $jobs = array();
        $rounds = $this->getRounds(json_decode($solutionApiResponse->getBody()));
        foreach ($rounds as $round) {
            $orderedDropoffs = $this->orderDropoffs($round, $dropoffs);
            $this->clearDropoffAt($orderedDropoffs);

            $job = new \Stuart\Job();
            $job->pushPickup($pickup);
            foreach ($orderedDropoffs as $dropoff) {
                $job->pushDropoff($dropoff);
            }
            $jobs[] = $job;
        }

        return (object)array(
            'jobs' => $jobs,
            'waste' => []
        );
    }

    private function orderDropoffs($rounds, $dropoffs)
    {
        $result = array();

        foreach ($rounds as $address) {
            $result[] = $this->matchDropoff($address, $dropoffs);
        }

        return $result;
    }

    private function matchDropoff($address, $dropoffs)
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
        $solutionApiResponse = $this->httpGetSolution($jobId);
        $solutionStatus = json_decode($solutionApiResponse->getBody())->status;

        while ($solutionStatus !== 'finished') {
            $solutionApiResponse = $this->httpGetSolution($jobId);
            $solutionStatus = json_decode($solutionApiResponse->getBody())->status;
        }

        return $solutionApiResponse;
    }

    private function getRounds($solution)
    {
        $rounds = array();
        foreach ($solution->solution->routes as $route) {
            unset($route->activities[0], $route->activities[1]);
            $addresses = array();
            foreach ($route->activities as $activity) {
                $addresses[] = $activity->address->location_id;
            }
            if (count($addresses) > 0) {
                $rounds[] = $addresses;
            }
        }

        return $rounds;
    }

    private function httpPostOptimize($pickup, $dropoffs)
    {
        $url = 'https://graphhopper.com/api/1/vrp/optimize?key=' . self::GRAPHHOPPER_API_KEY;
        $body = json_encode($this->optimizeRequestBody($pickup, $dropoffs));

        try {
            $response = $this->client->request('POST', $url, [
                'body' => $body,
                'headers' => ['Content-Type' => 'application/json']
            ]);
        } catch (RequestException $e) {
            if ($e->hasResponse()) {
                $response = $e->getResponse();
            } else {
                throw $e;
            }
        }

        return ApiResponseFactory::fromGuzzleHttpResponse($response);
    }

    private function httpGetSolution($jobId)
    {
        $url = 'https://graphhopper.com/api/1/vrp/solution/' . $jobId . '?key=' . self::GRAPHHOPPER_API_KEY;

        try {
            $response = $this->client->request('GET', $url, [
                'headers' => ['Content-Type' => 'application/json']
            ]);
        } catch (RequestException $e) {
            if ($e->hasResponse()) {
                $response = $e->getResponse();
            } else {
                throw $e;
            }
        }

        return ApiResponseFactory::fromGuzzleHttpResponse($response);
    }

    private function optimizeRequestBody($pickup, $dropoffs)
    {
        $slotSizeInMinutes = 30;

        $result = array();

        // vehicles
        $vehicles = $this->getVehicles($pickup, 10);

        $result['vehicles'] = $vehicles;

        // services
        $services = array();

        $services[] = array(
            'id' => $pickup->getAddress(),
            'address' => $this->getAddress($pickup)
        );

        foreach ($dropoffs as $dropoff) {
            $timeWindows = array();
            $timeWindows[] = array(
                'earliest' => $dropoff->getDropoffAt()->getTimestamp(),
                'latest' => $dropoff->getDropoffAt()->add(new \DateInterval('PT' . $slotSizeInMinutes . 'M'))->getTimestamp()
            );

            $services[] = array(
                'id' => $dropoff->getAddress(),
                'address' => $this->getAddress($dropoff),
                'time_windows' => $timeWindows
            );
        }
        $result['services'] = $services;

        return $result;
    }

    private function getAddress($location)
    {
        $geoloc = $this->geocode($location->getAddress());
        return array(
            'location_id' => $location->getAddress(),
            'lat' => $geoloc->lat,
            'lon' => $geoloc->lon
        );
    }

    private function geocode($fullTextAddress)
    {
        $url = 'https://graphhopper.com/api/1/geocode?q=' . $fullTextAddress . '&key=' . self::GRAPHHOPPER_API_KEY;

        try {
            $response = $this->client->request('GET', $url, [
                'headers' => ['Content-Type' => 'application/json']
            ]);
        } catch (RequestException $e) {
            if ($e->hasResponse()) {
                $response = $e->getResponse();
            } else {
                throw $e;
            }
        }

        $apiResponse = ApiResponseFactory::fromGuzzleHttpResponse($response);
        if ($apiResponse->success()) {
            $decodedBody = json_decode($apiResponse->getBody());
            return (object)array('lat' => $decodedBody->hits[0]->point->lat, 'lon' => $decodedBody->hits[0]->point->lng);
        }

        // handle failure
        return null;
    }

    private function getVehicles($pickup)
    {
        $vehicles = array();

        // TODO: configuration as parameter
        $vehicleCount = 10;
        $returnToDepot = false;
        $maxActivities = 9;

        while ($vehicleCount > 0) {
            $vehicles[] = array(
                'vehicle_id' => '000' . $vehicleCount
                ,
                'start_address' => $this->getAddress($pickup),
                'return_to_depot' => $returnToDepot,
                'max_activities' => $maxActivities
            );
            $vehicleCount--;
        }

        return $vehicles;
    }

    private function validateDropoffs($dropoffs)
    {
        // TODO: cannot have pickup at on the pikcup
        // TODO: all dropoffs must have dropoff_at
    }
}
