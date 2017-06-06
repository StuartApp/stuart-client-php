<?php

namespace Stuart\Repository;

use Stuart\Converters\JobToJson;
use Stuart\Converters\JsonToJob;

use Stuart\Helpers\ArrayHelper;
use Stuart\Infrastructure\HttpClient;
use Stuart\Job;

class JobRepository
{
    private $packageTypeIdMapping = [
        'small' => 1,
        'medium' => 2,
        'large' => 3,
        'extra_large' => 4
    ];


    /**
     * @var HttpClient
     */
    private $httpClient;

    /**
     * JobRepository constructor.
     * @param $httpClient
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
