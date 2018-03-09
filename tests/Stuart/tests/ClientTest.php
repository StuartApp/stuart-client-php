<?php

namespace Stuart\Tests;

use GuzzleHttp\Psr7\Response;
use Stuart\Client;
use Stuart\Infrastructure\Authenticator;
use Stuart\Infrastructure\HttpClient;

class ClientTest extends \PHPUnit_Framework_TestCase
{
    private $authenticator;
    private $httpClient;
    private $guzzleClient;
    private $client;

    /**
     * @var Mock
     */
    private $mock;

    public function setUp()
    {
        $this->authenticator = \Phake::mock(Authenticator::class);
        $this->guzzleClient = \Phake::mock(\GuzzleHttp\Client::class);
        $this->httpClient = new HttpClient($this->authenticator, $this->guzzleClient);
        $this->mock = new Mock();
        $this->client = new Client($this->httpClient);
    }

    public function test_cancel_a_job_returns_true()
    {
        \Phake::when($this->guzzleClient)->request(\Phake::anyParameters())->thenReturn(
            new Response(200, [], null)
        );

        self::assertTrue($this->client->cancelJob(123));
    }

	public function test_cancel_a_delivery_returns_true()
	{
		\Phake::when($this->guzzleClient)->request(\Phake::anyParameters())->thenReturn(
			new Response(200, [], null)
		);

		self::assertTrue($this->client->cancelDelivery(123));
	}


	public function test_it_cancels_a_job_with_correct_parameters()
    {
        \Phake::when($this->guzzleClient)->request(\Phake::anyParameters())->thenReturn(
            new Response(200, [], null)
        );

        $this->client->cancelJob(123);

        \Phake::verify($this->guzzleClient)->request(
            'POST',
            '/v2/jobs/123/cancel',
            new \PHPUnit_Framework_Constraint_ArraySubset([ 'body' => '' ])
        );
    }

    public function test_it_validate_a_job_with_correct_parameters()
    {
        \Phake::when($this->guzzleClient)->request(\Phake::anyParameters())->thenReturn(
            new Response(200, [], json_encode(array('valid' => true)))
        );

        self::assertTrue($this->client->validateJob($this->mock->job()));
    }
}
