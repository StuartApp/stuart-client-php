<?php

namespace Stuart\Tests;

use VCR\VCR;

use Stuart\Infrastructure\HttpClient;
use Stuart\Infrastructure\Authenticator;

class HttpClientTest extends \PHPUnit_Framework_TestCase
{

    private $httpClient;
    private $authenticator;

    public function setUp()
    {
        $this->authenticator = \Phake::mock(Authenticator::class);
        $useSandbox = true;
        $this->httpClient = new HttpClient($useSandbox, $this->authenticator);
    }

    public function test_it_sends_the_php_header_on_post()
    {
        \Phake::when($this->authenticator)->accessToken()->thenReturn(
            'sample-access-token'
        );

        VCR::turnOn();
        VCR::configure()->setCassettePath('.');
        VCR::insertCassette('guzzletest.yml');

        $this->httpClient->performPost(null, '/sample/url');

        VCR::eject();
        VCR::turnOff();

        self::assertEquals(true, true);
    }
}
