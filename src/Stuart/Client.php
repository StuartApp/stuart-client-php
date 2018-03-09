<?php

namespace Stuart;

use Stuart\Converters\JobToJson;
use Stuart\Converters\JsonToJob;

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

        if ($apiResponse->success()) {
            return json_decode($apiResponse->getBody())->valid;
        } else {
            return json_decode($apiResponse->getBody());
        }
    }

    public function createJob($job)
    {
        $body = JobToJson::convert($job);

        $apiResponse = $this->httpClient->performPost($body, '/v2/jobs');
        if ($apiResponse->success()) {
            return JsonToJob::convert($apiResponse->getBody());
        } else {
            return json_decode($apiResponse->getBody());
        }
    }

    public function getJob($jobId)
    {
        $apiResponse = $this->httpClient->performGet('/v2/jobs/' . $jobId);

        if ($apiResponse->success()) {
            return JsonToJob::convert($apiResponse->getBody());
        } else {
            return json_decode($apiResponse->getBody());
        }
    }

    public function cancelJob($jobId)
    {
        $apiResponse = $this->httpClient->performPost('', '/v2/jobs/' . $jobId . '/cancel');

        if ($apiResponse->success()) {
            return true;
        } else {
            return json_decode($apiResponse->getBody());
        }
    }

    public function cancelDelivery($deliveryId)
    {
        $apiResponse = $this->httpClient->performPost('', '/v2/deliveries/' . $deliveryId . '/cancel');

        if ($apiResponse->success()) {
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
}
