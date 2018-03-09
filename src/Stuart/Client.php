<?php

namespace Stuart;

use Stuart\Converters\JobToJson;
use Stuart\Repository\JobEtaRepository;
use Stuart\Repository\JobPricingRepository;
use Stuart\Repository\JobRepository;

class Client
{
    private $httpClient;
    private $jobRepository;
    private $jobPricingRepository;
    private $jobEtaRepository;

    public function __construct($httpClient)
    {
        $this->httpClient = $httpClient;
        $this->jobRepository = new JobRepository($this->httpClient);
        $this->jobPricingRepository = new JobPricingRepository($this->httpClient);
        $this->jobEtaRepository = new JobEtaRepository($this->httpClient);
    }

    /**
     * @param Job $job
     *
     * @return bool|\stdClass
     */
    public function validateJob(Job $job)
    {
        $body = JobToJson::convert($job);
        $apiResponse = $this->httpClient->performPost($body, '/v2/jobs/validate');

        if ($apiResponse->success()) {
            return json_decode($apiResponse->getBody())->valid;
        } else {
            return json_decode($apiResponse->getBody());
        }
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

	public function cancelDelivery($deliveryId)
	{
		$apiResponse = $this->httpClient->performPost('', '/v2/deliveries/' . $deliveryId . '/cancel');

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
