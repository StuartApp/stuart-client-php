<?php

namespace Stuart;

use Stuart\Infrastructure\HttpClient;
use Stuart\Repository\JobRepository;

class Client
{
    private $jobRepository;

    public function __construct($authenticator)
    {
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

    public function createStackedJob($job)
    {
        return $this->jobRepository->saveStackedJob($job);
    }
}