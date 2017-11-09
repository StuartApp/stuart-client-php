<?php

namespace Stuart;

use Stuart\Infrastructure\HttpClient;
use Stuart\Repository\JobRepository;
use Stuart\Repository\JobPricingRepository;

class Client
{
    private $jobRepository;
    private $jobPricingRepository;

    public function __construct($authenticator)
    {
        $guzzleClient = new \GuzzleHttp\Client();
        $httpClient = new HttpClient($authenticator, $guzzleClient);
        $this->jobRepository = new JobRepository($httpClient);
        $this->jobPricingRepository = new JobPricingRepository($httpClient);
    }

    public function createJob($job)
    {
        return $this->jobRepository->save($job);
    }

    public function getPricing($job)
    {
        return $this->jobPricingRepository->save($job);
    }

    public function getJob($jobId)
    {
        return $this->jobRepository->get($jobId);
    }
}
