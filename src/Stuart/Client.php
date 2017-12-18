<?php

namespace Stuart;

use Stuart\Infrastructure\HttpClient;
use Stuart\Repository\JobEtaRepository;
use Stuart\Repository\JobPricingRepository;
use Stuart\Repository\JobRepository;

class Client
{
    private $httpClient;
    private $jobRepository;
    private $jobPricingRepository;
    private $jobEtaRepository;

    public function __construct($authenticator, \GuzzleHttp\Client $client = null)
    {
        $guzzleClient = $client ?: new \GuzzleHttp\Client();
        $this->httpClient = new HttpClient($authenticator, $guzzleClient);
        $this->jobRepository = new JobRepository($this->httpClient);
        $this->jobPricingRepository = new JobPricingRepository($this->httpClient);
        $this->jobEtaRepository = new JobEtaRepository($this->httpClient);
    }

    public function createJob($job)
    {
        return $this->jobRepository->save($job);
    }

    public function getJob($jobId)
    {
        return $this->jobRepository->get($jobId);
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

    public function getPricing($job)
    {
        return $this->jobPricingRepository->save($job);
    }

    public function getEta($job)
    {
        return $this->jobEtaRepository->save($job);
    }
}
