<?php

namespace Stuart;

use Stuart\Infrastructure\HttpClient;
use Stuart\Repository\JobEtaRepository;
use Stuart\Repository\JobPricingRepository;
use Stuart\Repository\JobRepository;

class Client
{
    private $jobRepository;
    private $jobPricingRepository;
    private $jobEtaRepository;

    public function __construct($authenticator)
    {
        $guzzleClient = new \GuzzleHttp\Client();
        $httpClient = new HttpClient($authenticator, $guzzleClient);
        $this->jobRepository = new JobRepository($httpClient);
        $this->jobPricingRepository = new JobPricingRepository($httpClient);
        $this->jobEtaRepository = new JobEtaRepository($httpClient);
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

    public function getEta($job)
    {
        return $this->jobEtaRepository->save($job);
    }
}
