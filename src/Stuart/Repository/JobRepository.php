<?php

namespace Stuart\Repository;

use Stuart\Converters\JobToJson;
use Stuart\Converters\JsonToJob;

use Stuart\Infrastructure\HttpClient;

class JobRepository
{
    /**
     * @var HttpClient
     */
    private $httpClient;

    /**
     * JobRepository constructor.
     * @param HttpClient $httpClient
     */
    public function __construct($httpClient)
    {
        $this->httpClient = $httpClient;
    }

    public function save($job)
    {
        $body = JobToJson::convert($job);
        $apiResponse = $this->httpClient->performPost($body, '/v2/jobs');
        if ($apiResponse->success()) {
            return JsonToJob::convert($apiResponse->getBody());
        }
    }

    public function get($jobId)
    {
        $apiResponse = $this->httpClient->performGet('/v2/jobs/' . $jobId);
        if ($apiResponse->success()) {
            return JsonToJob::convert($apiResponse->getBody());
        }
    }
}
