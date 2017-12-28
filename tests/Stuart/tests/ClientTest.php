<?php

namespace Stuart\Tests;

use GuzzleHttp\Psr7\Response;
use Stuart\Client;
use Stuart\Infrastructure\Authenticator;

class ClientTest extends \PHPUnit_Framework_TestCase
{
    private $authenticator;
    private $httpClient;

    public function setUp()
    {
        $this->httpClient = \Phake::mock(\GuzzleHttp\Client::class);
        $this->authenticator = \Phake::mock(Authenticator::class);

        \Phake::when($this->httpClient)->request(\Phake::anyParameters())->thenReturn(
            new Response(200, [], null)
        );
    }

    public function test_cancel_a_job_returns_true()
    {
        $client = new Client($this->authenticator, $this->httpClient);

        self::assertTrue($client->cancelJob(123));
    }

    public function test_it_cancels_a_job_with_correct_parameters()
    {
        $client = new Client($this->authenticator, $this->httpClient);

        $client->cancelJob(123);

        \Phake::verify($this->httpClient)->request(
            'POST',
            '/v2/jobs/123/cancel',
            new \PHPUnit_Framework_Constraint_ArraySubset([ 'body' => '' ])
        );
    }
}
