<?php

namespace Stuart;

use Stuart\Converters\JobToJson;
use Stuart\Converters\JsonToJob;
use Stuart\Converters\JsonToSchedulingSlots;

class Client
{
    private $httpClient;

    public function __construct($httpClient)
    {
        $this->httpClient = $httpClient;
    }

    /**
     * @param Job $job
     *
     * @return bool|\stdClass
     */
    public function validateJob(Job $job)
    {
        $body = JobToJson::convert($job);
        $apiResponse = $this->httpClient->performPost($body, '/v2/jobs/validate');

        if (self::isSuccess($apiResponse)) {
            return json_decode($apiResponse->getBody())->valid;
        } else {
            return json_decode($apiResponse->getBody());
        }
    }

    public function getSchedulingSlotsAtPickup($city, \DateTime $dateTime)
    {
        return $this->getSchedulingSlots($city, 'pickup', $dateTime);
    }

    public function getSchedulingSlotsAtDropoff($city, \DateTime $dateTime)
    {
        return $this->getSchedulingSlots($city, 'dropoff', $dateTime);
    }

    private function getSchedulingSlots($city, $type, \DateTime $dateTime)
    {
        $apiResponse = $this->httpClient->performGet('/v2/jobs/schedules/' . $city . '/' . $type . '/' . $dateTime->format('Y-m-d'));

        if (self::isSuccess($apiResponse)) {
            return JsonToSchedulingSlots::convert($apiResponse->getBody());
        } else {
            return json_decode($apiResponse->getBody());
        }
    }

    public function createJob($job)
    {
        $body = JobToJson::convert($job);

        $apiResponse = $this->httpClient->performPost($body, '/v2/jobs');
        if (self::isSuccess($apiResponse)) {
            return JsonToJob::convert($apiResponse->getBody());
        } else {
            return json_decode($apiResponse->getBody());
        }
    }

    public function getJob($jobId)
    {
        $apiResponse = $this->httpClient->performGet('/v2/jobs/' . $jobId);

        if (self::isSuccess($apiResponse)) {
            return JsonToJob::convert($apiResponse->getBody());
        } else {
            return json_decode($apiResponse->getBody());
        }
    }

    public function cancelJob($jobId)
    {
        $apiResponse = $this->httpClient->performPost('', '/v2/jobs/' . $jobId . '/cancel');

        if (self::isSuccess($apiResponse)) {
            return true;
        } else {
            return json_decode($apiResponse->getBody());
        }
    }

    public function cancelDelivery($deliveryId)
    {
        $apiResponse = $this->httpClient->performPost('', '/v2/deliveries/' . $deliveryId . '/cancel');

        if (self::isSuccess($apiResponse)) {
            return true;
        } else {
            return json_decode($apiResponse->getBody());
        }
    }

    public function getPricing($job)
    {
        $body = JobToJson::convert($job);

        $apiResponse = $this->httpClient->performPost($body, '/v2/jobs/pricing');
        return json_decode($apiResponse->getBody());
    }

    public function getEta($job)
    {
        $body = JobToJson::convert($job);

        $apiResponse = $this->httpClient->performPost($body, '/v2/jobs/eta');
        return json_decode($apiResponse->getBody());
    }

    private function validateAddress($address, $type)
    {
        $query = array(
            'address' => $address,
            'type' => $type
        );

        $apiResponse = $this->httpClient->performGet('/v2/addresses/validate', $query);
        return json_decode($apiResponse->getBody());
    }

    public function validatePickupAddress($address)
    {
        return $this->validateAddress($address, 'picking');
    }

    public function validateDropoffAddress($address)
    {
        return $this->validateAddress($address, 'delivering');
    }

    private function isSuccess($response){
        $statusCode =$response->getStatusCode();
        return $statusCode >= 200 && $statusCode < 300;
    }
}
