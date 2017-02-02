<?php

namespace Stuart;

class Job
{
    private $httpClient;
    private $origin;
    private $destination;
    private $packageSize;

    /**
     * Job constructor.
     * @param $origin
     * @param $destination
     * @param $packageSize
     * @param $httpClient
     */
    public function __construct($origin, $destination, $packageSize, $httpClient)
    {
        $this->httpClient = $httpClient;
        $this->origin = $origin;
        $this->destination = $destination;
        $this->packageSize = $packageSize;
    }

    public function create()
    {
        $formParams = [
            'originAddressStreet' => $this->origin['address'],
            'originContactCompany' => $this->origin['company'],
            'originContactFirstname' => $this->origin['first_name'],
            'originContactLastname' => $this->origin['last_name'],
            'originContactPhone' => $this->origin['phone'],
            'destinationAddressStreet' => $this->destination['address'],
            'destinationContactCompany' => $this->destination['company'],
            'destinationContactFirstname' => $this->destination['first_name'],
            'destinationContactLastname' => $this->destination['last_name'],
            'destinationContactPhone' => $this->destination['phone'],
            'packageTypeId' => $this->computePackageTypeId()
        ];

        return $this->httpClient->performPost($formParams, '/v1/jobs/package');
    }

    private function computePackageTypeId()
    {
        if ($this->packageSize === 'small') {
            return 1;
        } elseif ($this->packageSize === 'medium') {
            return 2;
        } elseif ($this->packageSize === 'large') {
            return 3;
        } elseif ($this->packageSize === 'extra_large') {
            return 4;
        }
    }
}