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

    public function test_it_create_job_should_return_successfull_response()
    {
        // given
        \Phake::when($this->httpClient)->performPost(\Phake::anyParameters())->thenReturn(
            new ApiResponse(200, null)
        );

        $job = $this->sampleJob();

        // when
        $api_response = $this->jobRepository->save($job);

        // then
        self::assertTrue($api_response->success());
    }

    public function test_it_create_job_should_call_http_client_w_correct_parameters()
    {
        // given
        $job = $this->sampleJob();

        // when
        $this->jobRepository->save($job);

        // then
        $formParams = [
            'originAddressStreet' => '18 rue sidi brahim, 75012 Paris',
            'originContactCompany' => 'WeSellWine Inc.',
            'originContactFirstname' => 'Marcel',
            'originContactLastname' => 'Poisson',
            'originContactPhone' => '0628739512',
            'destinationAddressStreet' => '5 rue sidi brahim, 75012 Paris',
            'destinationContactCompany' => 'Jean-Marc SAS',
            'destinationContactFirstname' => 'Jean-Marc',
            'destinationContactLastname' => 'Pinchu',
            'destinationContactPhone' => '0628046934',
            'packageTypeId' => 1
        ];
        $resource = '/v1/jobs/package';

        \Phake::verify($this->httpClient)->performPost($formParams, $resource);
    }

    // TODO: integrate job creation (JobRepository.add($job) returns the Job with more data
    // TODO: scheduling
    // TODO: parameter validation

    // helpers
    private function sampleJob()
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
        $package_size = 'small';

        return new Job($origin, $destination, $package_size);
    }
}
