<?php

namespace Stuart\Tests;

use Stuart\Client;
use Stuart\Converters\JobToJson;
use Stuart\Infrastructure\ApiResponse;
use Stuart\Infrastructure\Authenticator;


class ClientTest extends \PHPUnit_Framework_TestCase
{
    private $authenticator;
    private $httpClient;
    private $client;

    private $mock;

    public function setUp()
    {
        $this->authenticator = \Phake::mock(Authenticator::class);
        $this->httpClient = \Phake::mock(\Stuart\Infrastructure\HttpClient::class);
        $this->mock = new Mock();
        $this->client = new Client($this->httpClient);
    }

    public function test_create_a_job()
    {
        \Phake::when($this->httpClient)->performPost(\Phake::anyParameters())->thenReturn(
            new ApiResponse(200, $this->mock->job_creation_response_json())
        );

        $job = $this->mock->job();
        $this->client->createJob($job);

        \Phake::verify($this->httpClient)->performPost(JobToJson::convert($job), '/v2/jobs');
        self::assertNotNull($this->client->createJob($this->mock->job()));
    }

    public function test_get_a_job()
    {
        \Phake::when($this->httpClient)->performGet(\Phake::anyParameters())->thenReturn(
            new ApiResponse(200, $this->mock->job_creation_response_json())
        );

        $sampleJobId = 123;
        $this->client->getJob($sampleJobId);

        \Phake::verify($this->httpClient)->performGet('/v2/jobs/' . $sampleJobId);
        self::assertNotNull($this->client->getJob(123));
    }

    public function test_cancel_a_job()
    {
        \Phake::when($this->httpClient)->performPost(\Phake::anyParameters())->thenReturn(
            new ApiResponse(200, null)
        );

        self::assertTrue($this->client->cancelJob(123));
        \Phake::verify($this->httpClient)->performPost(null, '/v2/jobs/123/cancel');
    }

    public function test_cancel_a_delivery()
    {
        \Phake::when($this->httpClient)->performPost(\Phake::anyParameters())->thenReturn(
            new ApiResponse(200, null)
        );

        self::assertTrue($this->client->cancelDelivery(123));
        \Phake::verify($this->httpClient)->performPost(null, '/v2/deliveries/123/cancel');
    }

    public function test_validate_a_job()
    {
        \Phake::when($this->httpClient)->performPost(\Phake::anyParameters())->thenReturn(
            new ApiResponse(200, json_encode(array('valid' => true)))
        );

        $job = $this->mock->job();

        self::assertTrue($this->client->validateJob($job));
        \Phake::verify($this->httpClient)->performPost(JobToJson::convert($job), '/v2/jobs/validate');
    }
}
