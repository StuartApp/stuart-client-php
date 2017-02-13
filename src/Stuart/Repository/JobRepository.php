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
        if ($job->getPickupAt()) {
            $formParams['pickupAt'] = $job->getPickupAt()->format(\DateTime::ATOM);
        }

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

        $origin = $this->getJobOrigin($body);
        $destination = $this->getJobDestination($body);
        $packageSize = array_search($body['packageType']['id'], $this->packageTypeIdMapping);
        $job = new Job($origin, $destination, $packageSize);

        $jobId = $body['id'];
        $job->enrich(
            [
                'id' => $jobId,
                'tracking_url' => $body['trackingUrl']
            ]
        );
        $job->schedulePickupAt(\DateTime::createFromFormat(\Datetime::ATOM, $body['pickupAt']));
        return $job;
    }

    /**
     * @param $body
     * @return array
     */
    private function getJobOrigin($body)
    {
        return $this->getJobAddress($body, 'originPlace');
    }

    /**
     * @param $body
     * @return array
     */
    private function getJobDestination($body)
    {
        return $this->getJobAddress($body, 'destinationPlace');
    }

    private function getJobAddress($body, $address_type)
    {
        $place = $body[$address_type];
        $address = $place['address'];
        $jobAddress = [
            'address' => "{$address['street']}, {$address['postCode']}, /
                          {$address['zone']['name']}",
            'company' => $place['contactCompany'],
            'first_name' => $place['contactFirstname'],
            'last_name' => $place['contactLastname'],
            'phone' => $place['contactPhone']
        ];
        return $jobAddress;
    }
}