<?php

namespace Stuart\Repository;


class JobRepository
{
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
        $origin = $job->getOrigin();
        $destination = $job->getDestination();
        $formParams = [
            'originAddressStreet' => $origin['address'],
            'originContactCompany' => $origin['company'],
            'originContactFirstname' => $origin['first_name'],
            'originContactLastname' => $origin['last_name'],
            'originContactPhone' => $origin['phone'],
            'destinationAddressStreet' => $destination['address'],
            'destinationContactCompany' => $destination['company'],
            'destinationContactFirstname' => $destination['first_name'],
            'destinationContactLastname' => $destination['last_name'],
            'destinationContactPhone' => $destination['phone'],
            'packageTypeId' => $this->computePackageTypeId($job)
        ];

        $apiResponse = $this->httpClient->performPost($formParams, '/v1/jobs/package');
        if ($apiResponse->success()) {
            return $apiResponse->getBody()['id'];
        }
    }

    private function computePackageTypeId($job)
    {
        if ($job->getPackageSize() === 'small') {
            return 1;
        } elseif ($job->getPackageSize() === 'medium') {
            return 2;
        } elseif ($job->getPackageSize() === 'large') {
            return 3;
        } elseif ($this->getPackageSize() === 'extra_large') {
            return 4;
        }
    }
}