<?php

namespace Stuart\Tests\Repository;

use Stuart\Converters\JobToJson;
use Stuart\Infrastructure\ApiResponse;
use Stuart\Repository\JobPricingRepository;
use Stuart\Tests\Mock;

class JobPricingRepositoryTest extends \PHPUnit_Framework_TestCase
{
    private $httpClient;
    private $jobPricingRepository;
    private $mock;

    public function setUp()
    {
        $this->httpClient = \Phake::mock(\Stuart\Infrastructure\HttpClient::class);
        $this->jobPricingRepository = new JobPricingRepository($this->httpClient);
        $this->mock = new Mock();

        \Phake::when($this->httpClient)->performPost(\Phake::anyParameters())->thenReturn(
            new ApiResponse(200, $this->mock->job_creation_response_json())
        );
    }

    public function test_it_creates_a_job_pricing_with_correct_parameters()
    {
        $job = $this->mock->job();
        $this->jobPricingRepository->save($job);
        \Phake::verify($this->httpClient)->performPost(JobToJson::convert($job), '/v2/jobs/pricing');
    }
}
