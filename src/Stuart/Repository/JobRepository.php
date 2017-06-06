<?php

namespace Stuart\Repository;

use Stuart\Converters\StackedJobToJson;
use Stuart\Converters\JsonToStackedJob;

use Stuart\Helpers\ArrayHelper;
use Stuart\Infrastructure\HttpClient;
use Stuart\Job;

class JobRepository
{
    private $packageTypeIdMapping = [
        'small' => 1,
        'medium' => 2,
        'large' => 3,
        'extra_large' => 4
    ];


    /**
     * @var HttpClient
     */
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
            'originAddressStreet' => ArrayHelper::getSafe($origin, 'address'),
            'originContactCompany' => ArrayHelper::getSafe($origin, 'company'),
            'originContactFirstname' => ArrayHelper::getSafe($origin, 'first_name'),
            'originContactLastname' => ArrayHelper::getSafe($origin, 'last_name'),
            'originContactPhone' => ArrayHelper::getSafe($origin, 'phone'),
            'destinationAddressStreet' => ArrayHelper::getSafe($destination, 'address'),
            'destinationContactCompany' => ArrayHelper::getSafe($destination, 'company'),
            'destinationContactFirstname' => ArrayHelper::getSafe($destination, 'first_name'),
            'destinationContactLastname' => ArrayHelper::getSafe($destination, 'last_name'),
            'destinationContactPhone' => ArrayHelper::getSafe($destination, 'phone'),
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

    public function saveStackedJob($job)
    {
        $body = StackedJobToJson::convert($job);
        $apiResponse = $this->httpClient->performPost($body, '/v2/jobs');
        if ($apiResponse->success()) {
            return JsonToStackedJob::convert($apiResponse->getBody());
        }
    }

    public function getStackedJob($jobId)
    {
        $apiResponse = $this->httpClient->performGet('/v2/jobs/' . $jobId);
        if ($apiResponse->success()) {
            return JsonToStackedJob::convert($apiResponse->getBody());
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
        $job->enrich(
            [
                'id' => $body->id,
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
            'address' => "{$address->street}, {$address->postcode}, {$address->zone->name}",
            'company' => $place->contactCompany,
            'first_name' => $place->contactFirstname,
            'last_name' => $place->contactLastname,
            'phone' => $place->contactPhone
        ];
        return $jobAddress;
    }
}