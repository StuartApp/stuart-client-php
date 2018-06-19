<?php

namespace Stuart\Tests;

use Stuart\Client;
use Stuart\Converters\JobToJson;
use Stuart\Infrastructure\ApiResponse;
use Stuart\Infrastructure\Authenticator;
use Stuart\SchedulingSlots;

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

    public function test_get_an_eta()
    {
        \Phake::when($this->httpClient)->performPost(\Phake::anyParameters())->thenReturn(
            new ApiResponse(200, $this->mock->job_eta_response_json())
        );

        $job = $this->mock->job();
        $eta = $this->client->getEta($job);

        self::assertEquals($eta->eta, 672);
        \Phake::verify($this->httpClient)->performPost(JobToJson::convert($job), '/v2/jobs/eta');
    }

    public function test_get_a_pricing()
    {
        \Phake::when($this->httpClient)->performPost(\Phake::anyParameters())->thenReturn(
            new ApiResponse(200, $this->mock->job_pricing_response_json())
        );

        $job = $this->mock->job();
        $pricing = $this->client->getPricing($job);

        self::assertEquals($pricing->amount, 11.5);
        self::assertEquals($pricing->currency, 'EUR');
        \Phake::verify($this->httpClient)->performPost(JobToJson::convert($job), '/v2/jobs/pricing');
    }

    public function test_validate_a_pickup_address()
    {
        \Phake::when($this->httpClient)->performGet(\Phake::anyParameters())->thenReturn(
            new ApiResponse(200, $this->mock->address_validate_response_json())
        );

        $address = $this->mock->pickup_address();
        $validity = $this->client->validatePickupAddress($address);

        $query = array(
            'address' => $address,
            'type' => 'picking'
        );

        self::assertEquals($validity->success, true);
        \Phake::verify($this->httpClient)->performGet('/v2/addresses/validate', $query);
    }

    public function test_validate_a_dropoff_address()
    {
        \Phake::when($this->httpClient)->performGet(\Phake::anyParameters())->thenReturn(
            new ApiResponse(200, $this->mock->address_validate_response_json())
        );

        $address = $this->mock->drop_off_address();
        $validity = $this->client->validateDropoffAddress($address);

        $query = array(
            'address' => $address,
            'type' => 'delivering'
        );

        self::assertEquals($validity->success, true);
        \Phake::verify($this->httpClient)->performGet('/v2/addresses/validate', $query);
    }

    public function test_get_scheduling_slots()
    {
        \Phake::when($this->httpClient)->performGet(\Phake::anyParameters())->thenReturn(
            new ApiResponse(200, $this->mock->scheduling_slots_response_json())
        );

        $city = 'London';
        $date = new \DateTime();
        $this->client->getSchedulingSlotsAtPickup($city, $date);

        \Phake::verify($this->httpClient)->performGet('/v2/jobs/schedules/London/pickup/'.$date->format('Y-m-d'));
        self::assertInstanceOf(SchedulingSlots::class, $this->client->getSchedulingSlotsAtPickup($city, $date));

        $city = 'Paris';
        $this->client->getSchedulingSlotsAtDropoff($city, $date);

        \Phake::verify($this->httpClient)->performGet('/v2/jobs/schedules/Paris/dropoff/'.$date->format('Y-m-d'));
        self::assertInstanceOf(SchedulingSlots::class, $this->client->getSchedulingSlotsAtDropoff($city, $date));
    }
}
