<?php

namespace Stuart;

use Stuart\Infrastructure\HttpClient;
use Stuart\Repository\JobPricingRepository;
use Stuart\Repository\JobRepository;

class Client
{
    private $jobRepository;
    private $jobPricingRepository;

    public function __construct($authenticator, \GuzzleHttp\Client $client = null)
    {
        $guzzleClient = $client ?: new \GuzzleHttp\Client();
        $httpClient = new HttpClient($authenticator, $guzzleClient);
        $this->jobRepository = new JobRepository($httpClient);
        $this->jobPricingRepository = new JobPricingRepository($httpClient);
    }

    public function createJob($job)
    {
        return $this->jobRepository->save($job);
    }

    public function getJob($jobId)
    {
        return $this->jobRepository->get($jobId);
    }

    public function getPricing($job)
    {
        return $this->jobPricingRepository->save($job);
    }
}
