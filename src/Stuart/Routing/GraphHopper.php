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
    private $pickup;
    private $dropoffs;
    private $config;

    /**
     * GraphHopper constructor.
     */
    public function __construct($pickup, $dropoffs, $config)
    {
        $this->client = new Client();
        $this->pickup = $pickup;
        $this->dropoffs = $dropoffs;
        $this->config = $config;
    }

    public function findRounds()
    {
        $optimizedApiResponse = $this->httpPostOptimize();
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
            $sortedDropoffs = $this->sortedDropoffs($this->parseForStops($route));
            if (count($sortedDropoffs) > 0) {
                $jobs[] = $this->buildJob($route->waiting_time, $sortedDropoffs);
            }
        }
        return (object)array(
            'jobs' => $jobs,
            'waste' => []
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
            $result[] = $this->matchDropoff($stop, $this->dropoffs);
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

    private function clearDropoffAt()
    {
        foreach ($this->dropoffs as $dropoff) {
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

    private function buildOptimizeRequestBody()
    {
        $result = array();

        $vehicles = $this->buildVehicles();
        $result['vehicles'] = $vehicles;

        $services = array();
        $services[] = array(
            'id' => $this->pickup->getAddress(),
            'address' => $this->buildAddress($this->pickup)
        );

        foreach ($this->dropoffs as $dropoff) {
            $timeWindows = array();
            $timeWindows[] = array(
                'earliest' => $dropoff->getDropoffAt()->getTimestamp(),
                'latest' => $dropoff->getDropoffAt()->add(new \DateInterval('PT' . $this->config['slot_size_in_minutes'] . 'M'))->getTimestamp()
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

    private function buildVehicles()
    {
        $vehicles = array();

        $vehicleCount = $this->config['vehicle_count'];
        while ($vehicleCount > 0) {
            $vehicles[] = array(
                'vehicle_id' => '000' . $vehicleCount,
                'start_address' => $this->buildAddress($this->pickup),
                'return_to_depot' => $this->config['return_trip'],
                'max_activities' => $this->config['max_dropoffs']
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
    private function httpPostOptimize()
    {
        $url = 'https://graphhopper.com/api/1/vrp/optimize?key=' . self::GRAPHHOPPER_API_KEY;
        $body = json_encode($this->buildOptimizeRequestBody($this->pickup, $this->dropoffs));

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
    private function validateDropoffs()
    {
        // TODO: cannot have pickup at on the pikcup
        // TODO: all dropoffs must have dropoff_at
    }
}
