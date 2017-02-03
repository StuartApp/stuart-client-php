<?php

namespace Stuart\tests;

use Stuart\Infrastructure\ApiResponse;
use Stuart\Repository\JobRepository;
use Stuart\Job;

class JobRepositoryTest extends \PHPUnit_Framework_TestCase
{
    private $httpClient;
    private $jobRepository;

    public function setUp()
    {
        $this->httpClient = \Phake::mock(\Stuart\Infrastructure\HttpClient::class);
        $this->jobRepository = new JobRepository($this->httpClient);
    }

    public function test_it_create_job_should_return_job_id()
    {
        // given
        \Phake::when($this->httpClient)->performPost(\Phake::anyParameters())->thenReturn(
            new ApiResponse(200, ['id' => '0001'])
        );

        $job = $this->sampleJob('small');

        // when
        $jobId = $this->jobRepository->save($job);

        // then
        self::assertEquals($jobId, 0001);
    }

    public function test_it_create_small_job_should_call_http_client_w_correct_parameters()
    {
        // given
        \Phake::when($this->httpClient)->performPost(\Phake::anyParameters())->thenReturn(
            new ApiResponse(200, ['id' => '0001'])
        );
        $job = $this->sampleJob('small');

        // when
        $this->jobRepository->save($job);

        // then
        $formParams = [
            'originAddressStreet' => $job->getOrigin()['address'],
            'originContactCompany' => $job->getOrigin()['company'],
            'originContactFirstname' => $job->getOrigin()['first_name'],
            'originContactLastname' => $job->getOrigin()['last_name'],
            'originContactPhone' => $job->getOrigin()['phone'],
            'destinationAddressStreet' => $job->getDestination()['address'],
            'destinationContactCompany' => $job->getDestination()['company'],
            'destinationContactFirstname' => $job->getDestination()['first_name'],
            'destinationContactLastname' => $job->getDestination()['last_name'],
            'destinationContactPhone' => $job->getDestination()['phone'],
            'packageTypeId' => 1
        ];
        $resource = '/v1/jobs/package';

        \Phake::verify($this->httpClient)->performPost($formParams, $resource);
    }

    public function test_it_create_medium_job_should_call_http_client_w_correct_parameters()
    {
        // given
        \Phake::when($this->httpClient)->performPost(\Phake::anyParameters())->thenReturn(
            new ApiResponse(200, ['id' => '0001'])
        );
        $job = $this->sampleJob('medium');

        // when
        $this->jobRepository->save($job);

        // then
        $formParams = [
            'originAddressStreet' => $job->getOrigin()['address'],
            'originContactCompany' => $job->getOrigin()['company'],
            'originContactFirstname' => $job->getOrigin()['first_name'],
            'originContactLastname' => $job->getOrigin()['last_name'],
            'originContactPhone' => $job->getOrigin()['phone'],
            'destinationAddressStreet' => $job->getDestination()['address'],
            'destinationContactCompany' => $job->getDestination()['company'],
            'destinationContactFirstname' => $job->getDestination()['first_name'],
            'destinationContactLastname' => $job->getDestination()['last_name'],
            'destinationContactPhone' => $job->getDestination()['phone'],
            'packageTypeId' => 2
        ];
        $resource = '/v1/jobs/package';

        \Phake::verify($this->httpClient)->performPost($formParams, $resource);
    }

    public function test_it_create_large_job_should_call_http_client_w_correct_parameters()
    {
        // given
        \Phake::when($this->httpClient)->performPost(\Phake::anyParameters())->thenReturn(
            new ApiResponse(200, ['id' => '0001'])
        );
        $job = $this->sampleJob('large');

        // when
        $this->jobRepository->save($job);

        // then
        $formParams = [
            'originAddressStreet' => $job->getOrigin()['address'],
            'originContactCompany' => $job->getOrigin()['company'],
            'originContactFirstname' => $job->getOrigin()['first_name'],
            'originContactLastname' => $job->getOrigin()['last_name'],
            'originContactPhone' => $job->getOrigin()['phone'],
            'destinationAddressStreet' => $job->getDestination()['address'],
            'destinationContactCompany' => $job->getDestination()['company'],
            'destinationContactFirstname' => $job->getDestination()['first_name'],
            'destinationContactLastname' => $job->getDestination()['last_name'],
            'destinationContactPhone' => $job->getDestination()['phone'],
            'packageTypeId' => 3
        ];
        $resource = '/v1/jobs/package';

        \Phake::verify($this->httpClient)->performPost($formParams, $resource);
    }

    public function test_it_create_extra_large_job_should_call_http_client_w_correct_parameters()
    {
        // given
        \Phake::when($this->httpClient)->performPost(\Phake::anyParameters())->thenReturn(
            new ApiResponse(200, ['id' => '0001'])
        );
        $job = $this->sampleJob('extra_large');

        // when
        $this->jobRepository->save($job);

        // then
        $formParams = [
            'originAddressStreet' => $job->getOrigin()['address'],
            'originContactCompany' => $job->getOrigin()['company'],
            'originContactFirstname' => $job->getOrigin()['first_name'],
            'originContactLastname' => $job->getOrigin()['last_name'],
            'originContactPhone' => $job->getOrigin()['phone'],
            'destinationAddressStreet' => $job->getDestination()['address'],
            'destinationContactCompany' => $job->getDestination()['company'],
            'destinationContactFirstname' => $job->getDestination()['first_name'],
            'destinationContactLastname' => $job->getDestination()['last_name'],
            'destinationContactPhone' => $job->getDestination()['phone'],
            'packageTypeId' => 4
        ];
        $resource = '/v1/jobs/package';

        \Phake::verify($this->httpClient)->performPost($formParams, $resource);
    }

    // TODO: scheduling
    // TODO: parameter validation

    // helpers
    private function sampleJob($size)
    {
        $origin = [
            'address' => '18 rue sidi brahim, 75012 Paris',
            'company' => 'WeSellWine Inc.',
            'first_name' => 'Marcel',
            'last_name' => 'Poisson',
            'phone' => '0628739512'
        ];
        $destination = [
            'address' => '5 rue sidi brahim, 75012 Paris',
            'company' => 'Jean-Marc SAS',
            'first_name' => 'Jean-Marc',
            'last_name' => 'Pinchu',
            'phone' => '0628046934'
        ];
        $package_size = $size;

        return new Job($origin, $destination, $package_size);
    }
}
