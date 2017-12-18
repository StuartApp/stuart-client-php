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
}