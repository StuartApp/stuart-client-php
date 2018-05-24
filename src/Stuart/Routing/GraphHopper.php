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
        return (object)[$pickup, $computedDropoffs, $wastedDropoffs];
    }

    public function validateDropoffs($dropoffs)
    {
        // cannot have pickup at on the pikcup

    }

    public function optimize($pickup, $dropoffs)
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

    public function solution($jobId)
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

    public function getRequest($pickup, $dropoffs)
    {
        $result = array();

        // vehicles
        $vehicles = array();
        $vehicles[] = array(
            'vehicle_id' => '0001',
            'start_address' => $this->getAddress($pickup)
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
        return (object)array('lat' => 0.123, 'lon' => 2.1234);
    }
}
