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


    public function findRound($pickup, $dropoffs)
    {
        $computedDropoffs = [];
        $wastedDropoffs = [];

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

        $orderedAddresses = $this->getOrderedAddresses(json_decode($solutionApiResponse->getBody()));

        print_r($orderedAddresses);

        return (object)[$pickup, $computedDropoffs, $wastedDropoffs];
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

    private function getOrderedAddresses($solution)
    {
        unset($solution->solution->routes[0]->activities[0], $solution->solution->routes[0]->activities[1]);

        $addresses = array();

        foreach ($solution->solution->routes[0]->activities as $activity) {
            $addresses[] = $activity->address->location_id;
        }

        return $addresses;
    }

    private function validateDropoffs($dropoffs)
    {
        // TODO: cannot have pickup at on the pikcup
    }

    private function httpPostOptimize($pickup, $dropoffs)
    {
        $url = 'https://graphhopper.com/api/1/vrp/optimize?key=' . self::GRAPHHOPPER_API_KEY;
        $body = json_encode($this->getRequest($pickup, $dropoffs));

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

    private function getRequest($pickup, $dropoffs)
    {
        $result = array();

        // vehicles
        $vehicles = array();
        // TODO: configuration as parameter
        $vehicles[] = array(
            'vehicle_id' => '0001',
            'start_address' => $this->getAddress($pickup),
            'return_to_depot' => false,
            //'max_activities' => 8
        );
        $result['vehicles'] = $vehicles;

        // services
        $services = array();

        $services[] = array(
            'id' => $pickup->getAddress(),
            'address' => $this->getAddress($pickup)
        );

        foreach ($dropoffs as $dropoff) {
            $services[] = array(
                'id' => $dropoff->getAddress(),
                'address' => $this->getAddress($dropoff)
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
}
