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
            'packageTypeId' => $this->packageTypeIdMapping[$job->getPackageSize()],
            'clientReference' => $job->getClientReference()
        ];
        if ($job->getPickupAt()) {
            $formParams['pickupAt'] = $job->getPickupAt()->format(\DateTime::ATOM);
        }

        $apiResponse = $this->httpClient->performPost($formParams, '/v1/jobs/package');
        if ($apiResponse->success()) {
            return $this->getJobFromBody($apiResponse->getBody());
        }
    }

    public function get($jobId)
    {
        $apiResponse = $this->httpClient->performGet('/v1/jobs/' . $jobId);
        if (!$apiResponse->success()) {
            return null;
        }

        return $this->getJobFromBody($apiResponse->getBody());
    }

    /**
     * @param $body
     * @return Job
     */
    private function getJobFromBody($body)
    {
        $origin = $this->getJobOrigin($body);
        $destination = $this->getJobDestination($body);
        $packageSize = array_search($body->packageType->id, $this->packageTypeIdMapping);
        $options = ['pickup_at' => \DateTime::createFromFormat(\Datetime::ATOM, $body->pickupAt)];

        $job = new Job($origin, $destination, $packageSize, $options);

        $jobId = $body->id;
        $job->enrich(
            [
                'id' => $jobId,
                'tracking_url' => $body->trackingUrl
            ]
        );

        return $job;
    }

    /**
     * @param $body
     * @return array
     */
    private function getJobOrigin($body)
    {
        return $this->getJobAddress($body->originPlace);
    }

    /**
     * @param $body
     * @return array
     */
    private function getJobDestination($body)
    {
        return $this->getJobAddress($body->destinationPlace);
    }

    private function getJobAddress($place)
    {
        $address = $place->address;
        $jobAddress = [
            'address' => "{$address->street}, {$address->postCode}, /
                          {$address->zone->name}",
            'company' => $place->contactCompany,
            'first_name' => $place->contactFirstname,
            'last_name' => $place->contactLastname,
            'phone' => $place->contactPhone
        ];
        return $jobAddress;
    }
}