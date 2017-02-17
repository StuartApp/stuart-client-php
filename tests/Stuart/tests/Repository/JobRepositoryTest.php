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

    public function test_it_create_job_should_return_job()
    {
        // given
        \Phake::when($this->httpClient)->performPost(\Phake::anyParameters())->thenReturn(
            new ApiResponse(200, $this->sampleStuartJobResponse())
        );

        $job = $this->sampleJob('small', []);

        // when
        $job = $this->jobRepository->save($job);

        // then
        self::assertEquals($job->getId(), 0001);
    }

    public function test_it_create_small_job_should_call_http_client_w_correct_parameters()
    {
        // given
        \Phake::when($this->httpClient)->performPost(\Phake::anyParameters())->thenReturn(
            new ApiResponse(200, $this->sampleStuartJobResponse())
        );
        $job = $this->sampleJob('small', []);

        // when
        $this->jobRepository->save($job);

        // then
        $formParams = $this->getFormParam($job, 1, null);
        $resource = '/v1/jobs/package';

        \Phake::verify($this->httpClient)->performPost($formParams, $resource);
    }

    public function test_it_create_medium_job_should_call_http_client_w_correct_parameters()
    {
        // given
        \Phake::when($this->httpClient)->performPost(\Phake::anyParameters())->thenReturn(
            new ApiResponse(200, $this->sampleStuartJobResponse())
        );
        $job = $this->sampleJob('medium', []);

        // when
        $this->jobRepository->save($job);

        // then
        $formParams = $this->getFormParam($job, 2, null);
        $resource = '/v1/jobs/package';

        \Phake::verify($this->httpClient)->performPost($formParams, $resource);
    }

    public function test_it_create_large_job_should_call_http_client_w_correct_parameters()
    {
        // given
        \Phake::when($this->httpClient)->performPost(\Phake::anyParameters())->thenReturn(
            new ApiResponse(200, $this->sampleStuartJobResponse())
        );
        $job = $this->sampleJob('large', []);

        // when
        $this->jobRepository->save($job);

        // then
        $formParams = $this->getFormParam($job, 3, null);
        $resource = '/v1/jobs/package';

        \Phake::verify($this->httpClient)->performPost($formParams, $resource);
    }

    public function test_it_create_extra_large_job_should_call_http_client_w_correct_parameters()
    {
        // given
        \Phake::when($this->httpClient)->performPost(\Phake::anyParameters())->thenReturn(
            new ApiResponse(200, $this->sampleStuartJobResponse())
        );
        $job = $this->sampleJob('extra_large', []);

        // when
        $this->jobRepository->save($job);

        // then
        $formParams = $this->getFormParam($job, 4, null);
        $resource = '/v1/jobs/package';

        \Phake::verify($this->httpClient)->performPost($formParams, $resource);
    }

    public function test_it_create_a_scheduled_job_w_correct_parameters()
    {
        // given
        \Phake::when($this->httpClient)->performPost(\Phake::anyParameters())->thenReturn(
            new ApiResponse(200, $this->sampleStuartJobResponse())
        );
        $job = $this->sampleJob('small', ['pickup_at' => $this->getPickupAtDatetime()]);

        // when
        $this->jobRepository->save($job);

        // then
        $formParams = $this->getFormParam($job, 1, $this->getPickupAtDatetime()->format(\DateTime::ATOM));
        $resource = '/v1/jobs/package';

        \Phake::verify($this->httpClient)->performPost($formParams, $resource);
    }

    public function test_it_create_a_job_returns_a_scheduled_job()
    {
        // given
        \Phake::when($this->httpClient)->performPost(\Phake::anyParameters())->thenReturn(
            new ApiResponse(200, $this->sampleStuartScheduledJobResponse())
        );
        \Phake::when($this->httpClient)->performGet(\Phake::anyParameters())->thenReturn(
            new ApiResponse(200, $this->sampleStuartScheduledJobResponse())
        );
        $pickupAtDateTime = $this->getPickupAtDatetime();
        $job = $this->sampleJob('small', ['pickup_at' => $pickupAtDateTime]);

        // when
        $job = $this->jobRepository->save($job);

        // then
        $stuartJob = $this->jobRepository->get($job->getId());
        self::assertEquals($stuartJob->getPickupAt(), $pickupAtDateTime);
    }

    public function test_it_get_a_job_should_call_http_client_w_correct_parameters()
    {
        // given
        \Phake::when($this->httpClient)->performPost(\Phake::anyParameters())->thenReturn(
            new ApiResponse(200, $this->sampleStuartJobResponse())
        );
        \Phake::when($this->httpClient)->performGet(\Phake::anyParameters())->thenReturn(
            new ApiResponse(200, $this->sampleStuartJobResponse())
        );
        $job = $this->sampleJob('extra_large', []);
        $job = $this->jobRepository->save($job);

        // when
        $this->jobRepository->get($job->getId());

        // then
        \Phake::verify($this->httpClient)->performGet('/v1/jobs/' . $job->getId());
    }

    public function test_it_get_a_job_should_return_new_job()
    {
        // given
        \Phake::when($this->httpClient)->performPost(\Phake::anyParameters())->thenReturn(
            new ApiResponse(200, $this->sampleStuartJobResponse())
        );
        \Phake::when($this->httpClient)->performGet(\Phake::anyParameters())->thenReturn(
            new ApiResponse(200, $this->sampleStuartJobResponse())
        );
        $job = $this->sampleJob('extra_large', []);
        $job = $this->jobRepository->save($job);

        // when
        $stuartJob = $this->jobRepository->get($job->getId());

        // then
        self::assertEquals($stuartJob->getId(), $job->getId());
        self::assertNotNull($stuartJob->getTrackingUrl());
    }

    // helpers
    private function sampleJob($size, $options)
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

        return new Job($origin, $destination, $package_size, $options);
    }

    private function sampleStuartJobResponse()
    {
        return [
            'id' => '0001',
            'trackingUrl' => 'http',
        ];
    }

    private function sampleStuartScheduledJobResponse()
    {
        $result = $this->sampleStuartJobResponse();
        $result['pickupAt'] = $this->getPickupAtDatetime()->format(\DateTime::ATOM);
        return $result;
    }

    private function getFormParam($job, $packageTypeId, $pickupAt)
    {
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
            'packageTypeId' => $packageTypeId
        ];
        if ($pickupAt) {
            $formParams['pickupAt'] = $pickupAt;
        }
        return $formParams;
    }

    private function getPickupAtDatetime()
    {
        $pickupAt = new \DateTime('now');
        $pickupAt->add(new \DateInterval('P1D'));
        return $pickupAt;
    }
}
