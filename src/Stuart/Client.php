<?php

namespace Stuart;

use Stuart\Infrastructure\Authenticator;
use Stuart\Infrastructure\HttpClient;
use Stuart\Repository\JobRepository;

class Client
{
    private $authenticator;
    private $httpClient;


    public function __construct($environment, $api_client_id, $api_client_secret)
    {
        $this->authenticator = new Authenticator($environment, $api_client_id, $api_client_secret);
        $this->httpClient = new HttpClient($this->authenticator);
        $this->jobRepository = new JobRepository($this->httpClient);
    }

    public function createJob($job) {
        return $this->jobRepository->save($job);
    }

    public function getJob($jobId) {
        return $this->jobRepository->get($jobId);
    }
}