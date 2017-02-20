<?php

namespace Stuart;

use Stuart\Infrastructure\Authenticator;
use Stuart\Infrastructure\HttpClient;
use Stuart\Repository\JobRepository;

class Client
{
    private $jobRepository;

    public function __construct($environment, $api_client_id, $api_client_secret)
    {
        $authenticator = new Authenticator($environment, $api_client_id, $api_client_secret);
        $guzzleClient = new \GuzzleHttp\Client();
        $httpClient = new HttpClient($authenticator, $guzzleClient);
        $this->jobRepository = new JobRepository($httpClient);
    }

    public function createJob($job)
    {
        return $this->jobRepository->save($job);
    }

    public function getJob($jobId)
    {
        return $this->jobRepository->get($jobId);
    }
}