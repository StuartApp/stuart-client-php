<?php

namespace Stuart\Repository;

use Stuart\Job;

class JobRepository
{
    private $packageTypeIdMapping = [
        'small' => 1,
        'medium' => 2,
        'large' => 3,
        'extra_large' => 4
    ];

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
            'packageTypeId' => $this->packageTypeIdMapping[$job->getPackageSize()]
        ];

        $apiResponse = $this->httpClient->performPost($formParams, '/v1/jobs/package');
        if ($apiResponse->success()) {
            return $apiResponse->getBody()['id'];
        }
    }

    public function get($jobId)
    {
        $apiResponse = $this->httpClient->performGet('/v1/jobs/' . $jobId);
        if (!$apiResponse->success()) {
            return null;
        }

        $body = $apiResponse->getBody();

        $jobId = $body['id'];

        $originPlace = $body['originPlace'];
        $originAddress = $originPlace['address'];
        $origin = [
            'address' => "{$originAddress['street']}, {$originAddress['postCode']}, /
                          {$originAddress['zone']['name']}",
            'company' => $originPlace['contactCompany'],
            'first_name' => $originPlace['contactFirstname'],
            'last_name' => $originPlace['contactLastname'],
            'phone' => $originPlace['contactPhone']
        ];

        $destinationPlace = $body['destinationPlace'];
        $destinationAddress = $originPlace['address'];
        $destination = [
            'address' => "{$destinationAddress['street']}, {$destinationAddress['postCode']}, /
                          {$destinationAddress['zone']['name']}",
            'company' => $destinationPlace['contactCompany'],
            'first_name' => $destinationPlace['contactFirstname'],
            'last_name' => $destinationPlace['contactLastname'],
            'phone' => $destinationPlace['contactPhone']
        ];

        $packageTypeId = $body['packageType']['id'];
        $packageSize = array_search($packageTypeId, $this->packageTypeIdMapping);

        return new Job($jobId, $origin, $destination, $packageSize);
    }
}