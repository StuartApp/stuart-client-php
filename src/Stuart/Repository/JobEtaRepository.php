<?php

namespace Stuart\Repository;

use Stuart\Converters\JobToJson;
use Stuart\Infrastructure\HttpClient;

class JobEtaRepository
{
    /**
     * @var HttpClient
     */
    private $httpClient;

    /**
     * JobPricingRepository constructor.
     * @param HttpClient $httpClient
     */
    public function __construct($httpClient)
    {
        $this->httpClient = $httpClient;
    }

    public function save($job)
    {
        $body = JobToJson::convert($job);
        $apiResponse = $this->httpClient->performPost($body, '/v2/jobs/eta');
        return json_decode($apiResponse->getBody());
    }
}
