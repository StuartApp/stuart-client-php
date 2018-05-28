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
        $routes = json_decode($solutionApiResponse->getBody())->solution->routes;
        foreach ($routes as $route) {
            $sortedDropoffs = $this->sortDropoffs($dropoffs, $this->parseForStops($route));
            $jobs[] = $this->buildJob($pickup, $route->waiting_time, $sortedDropoffs);
        }
        return (object)array(
            'jobs' => $jobs,
            'waste' => []
        );
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


    private function sortDropoffs($dropoffs, $stops)
    {
        $result = array();

        foreach ($stops as $stop) {
            $result[] = $this->matchDropoff($stop, $dropoffs);
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
        if (count($addresses) > 0) {
            return $addresses;
        }

        return array();
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

    private function buildOptimizeRequestBody($pickup, $dropoffs)
    {
        $slotSizeInMinutes = 30;

        $result = array();

        // vehicles
        $vehicles = $this->buildVehicles($pickup, 10);

        $result['vehicles'] = $vehicles;

        // services
        $services = array();

        $services[] = array(
            'id' => $pickup->getAddress(),
            'address' => $this->buildAddress($pickup)
        );

        foreach ($dropoffs as $dropoff) {
            $timeWindows = array();
            $timeWindows[] = array(
                'earliest' => $dropoff->getDropoffAt()->getTimestamp(),
                'latest' => $dropoff->getDropoffAt()->add(new \DateInterval('PT' . $slotSizeInMinutes . 'M'))->getTimestamp()
            );

            $services[] = array(
                'id' => $dropoff->getAddress(),
                'address' => $this->buildAddress($dropoff),
                'time_windows' => $timeWindows
            );
        }
        $result['services'] = $services;

        return $result;
    }

    private function buildVehicles($pickup)
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
                'start_address' => $this->buildAddress($pickup),
                'return_to_depot' => $returnToDepot,
                'max_activities' => $maxActivities
            );
            $vehicleCount--;
        }

        return $vehicles;
    }

    private function buildAddress($location)
    {
        $geoloc = $this->geocode($location->getAddress());
        return array(
            'location_id' => $location->getAddress(),
            'lat' => $geoloc->lat,
            'lon' => $geoloc->lon
        );
    }

    // HTTP calls
    private function httpPostOptimize($pickup, $dropoffs)
    {
        $url = 'https://graphhopper.com/api/1/vrp/optimize?key=' . self::GRAPHHOPPER_API_KEY;
        $body = json_encode($this->buildOptimizeRequestBody($pickup, $dropoffs));

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

    // Validators
    private function validateDropoffs($dropoffs)
    {
        // TODO: cannot have pickup at on the pikcup
        // TODO: all dropoffs must have dropoff_at
    }
}
