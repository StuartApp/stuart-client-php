<?php

namespace Stuart\Tests\Repository;

use Stuart\Converters\JobToJson;
use Stuart\Infrastructure\ApiResponse;
use Stuart\Repository\JobRepository;
use Stuart\Tests\Mock;

class JobRepositoryTest extends \PHPUnit_Framework_TestCase
{
    private $httpClient;
    private $jobRepository;
    private $mock;

    public function setUp()
    {
        $this->httpClient = \Phake::mock(\Stuart\Infrastructure\HttpClient::class);
        $this->jobRepository = new JobRepository($this->httpClient);
        $this->mock = new Mock();

        \Phake::when($this->httpClient)->performGet(\Phake::anyParameters())->thenReturn(
            new ApiResponse(200, $this->mock->job_creation_response_json())
        );

        \Phake::when($this->httpClient)->performPost(\Phake::anyParameters())->thenReturn(
            new ApiResponse(200, $this->mock->job_creation_response_json())
        );

        \Phake::when($this->httpClient)->performPost(\Phake::equalTo(''), $this->stringEndsWith('cancel'))->thenReturn(
            new ApiResponse(200, '')
        );
    }

    public function test_it_creates_a_job_with_correct_parameters()
    {
        $job = $this->mock->job();
        $this->jobRepository->save($job);
        \Phake::verify($this->httpClient)->performPost(JobToJson::convert($job), '/v2/jobs');
    }

    public function test_it_gets_a_job_with_correct_parameters()
    {
        $sampleJobId = 123;
        $this->jobRepository->get($sampleJobId);
        \Phake::verify($this->httpClient)->performGet('/v2/jobs/' . $sampleJobId);
    }

    public function test_it_cancels_a_job_with_correct_parameters()
    {
        $sampleJobId = 123;
        $this->jobRepository->cancel($sampleJobId);
        \Phake::verify($this->httpClient)->performPost('', '/v2/jobs/' . $sampleJobId . '/cancel');
    }

    public function test_create_a_job_returns_a_job()
    {
        self::assertNotNull($this->jobRepository->get(123));
    }

    public function test_get_a_job_returns_a_job()
    {
        self::assertNotNull($this->jobRepository->save($this->mock->job()));
    }

    public function test_cancel_a_job_returns_true()
    {
        self::assertTrue($this->jobRepository->cancel(123));
    }
}
