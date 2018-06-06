<?php

namespace Stuart\Routing\GraphHopper;

use GuzzleHttp\Exception\RequestException;
use Stuart\Infrastructure\ApiResponseFactory;

class Client
{
    private $apiKey;
    private $client;

    public function __construct($apiKey, $client = null)
    {
        $this->client = $client === null ? new \GuzzleHttp\Client() : $client;
        $this->apiKey = $apiKey;
    }

    public function optimize($pickup, $dropoffs, $config)
    {
        $url = 'https://graphhopper.com/api/1/vrp/optimize?key=' . $this->apiKey;

        try {
            $response = $this->client->request('POST', $url, [
                'body' => json_encode($this->buildOptimizeQuery($pickup, $dropoffs, $config)),
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

    public function buildOptimizeQuery($pickup, $dropoffs, $config)
    {
        $result = array();

        $geocodedPickupAddress = $this->geocode($pickup->getAddress());
        $result['vehicles'] = Formatter::convertToVehicles($pickup->getAddress(), $geocodedPickupAddress->lat, $geocodedPickupAddress->lon,
            $config['vehicle_count'], $config['max_dropoffs'], $config['max_distance']);

        $services = array();
        $services[] = array(
            'id' => $pickup->getAddress(),
            'address' => Formatter::convertToAddress($pickup->getAddress(), $geocodedPickupAddress->lat, $geocodedPickupAddress->lon)
        );

        foreach ($dropoffs as $dropoff) {
            $timeWindows = array();
            $timeWindows[] = array(
                'earliest' => $dropoff->getDropoffAt()->getTimestamp(),
                'latest' => $dropoff->getDropoffAt()->add(new \DateInterval('PT' . $config['slot_size_in_minutes'] . 'M'))->getTimestamp()
            );

            $geocodedDropoffAddress = $this->geocode($dropoff->getAddress());
            $services[] = array(
                'id' => $dropoff->getAddress(),
                'address' => Formatter::convertToAddress($dropoff->getAddress(), $geocodedDropoffAddress->lat, $geocodedDropoffAddress->lon),
                'time_windows' => $timeWindows
            );
        }
        $result['services'] = $services;

        return $result;
    }

    public function geocode($fullTextAddress)
    {
        $url = 'https://graphhopper.com/api/1/geocode?q=' . $fullTextAddress . '&key=' . $this->apiKey;

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

    public function solution($jobId)
    {
        $url = 'https://graphhopper.com/api/1/vrp/solution/' . $jobId . '?key=' . $this->apiKey;

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
}
