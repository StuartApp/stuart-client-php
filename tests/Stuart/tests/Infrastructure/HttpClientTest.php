<?php

namespace Stuart\Tests\Infrastructure;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Response;
use Stuart\Infrastructure\Authenticator;
use Stuart\Infrastructure\Environment;
use Stuart\Infrastructure\HttpClient;

class HttpClientTest extends \PHPUnit_Framework_TestCase
{
    const PHP_CLIENT_USER_AGENT = 'stuart-php-client/3.5.0';
    private $authenticator;
    private $container;

    public function setUp()
    {
        $this->authenticator = \Phake::mock(Authenticator::class);
        \Phake::when($this->authenticator)->getEnvironment()->thenReturn(
            Environment::SANDBOX
        );
        \Phake::when($this->authenticator)->accessToken()->thenReturn(
            'sample-access-token'
        );

        $this->container = array();
    }

    public function test_it_sends_the_php_lib_version_header_on_get()
    {
        $httpClient = $this->httpClientWith200OK();
        $httpClient->performGet('/test');

        foreach ($this->container as $transaction) {
            $userAgent = $transaction['request']->getHeaders()['User-Agent'][0];
            self::assertEquals(self::PHP_CLIENT_USER_AGENT, $userAgent);
        }
    }

    private function httpClientWith200OK()
    {
        $history = Middleware::history($this->container);
        $mock = new MockHandler([
            new Response(200, ['X - Foo' => 'Bar'])
        ]);
        $handler = HandlerStack::create($mock);
        $handler->push($history);

        $client = new Client(['handler' => $handler]);
        $httpClient = new HttpClient($this->authenticator, $client);

        return $httpClient;
    }

    public function test_it_sends_the_php_lib_version_header_on_post()
    {
        $httpClient = $this->httpClientWith200OK();
        $httpClient->performPost(null, '/test');

        foreach ($this->container as $transaction) {
            $userAgent = $transaction['request']->getHeaders()['User-Agent'][0];
            self::assertEquals(self::PHP_CLIENT_USER_AGENT, $userAgent);
        }
    }

    public function test_it_get_with_the_proper_parameters()
    {
        $httpClient = $this->httpClientWith200OK();
        $httpClient->performGet('/test');

        foreach ($this->container as $transaction) {
            self::assertEquals('GET', $transaction['request']->getMethod());
            self::assertEquals('/test', $transaction['request']->getUri()->getPath());
        }
    }

    public function test_it_gets_correct_error_response()
    {
        $httpClient = $this->httpClientWith422CantGeocodeAddress();
        $response = $httpClient->performGet('/test');
        $response_as_object = json_decode($response->getBody());

        self::assertEquals("CANT_GEOCODE_ADDRESS", $response_as_object->error);
        self::assertEquals("The address can't be geocoded", $response_as_object->message);
    }

    private function httpClientWith422CantGeocodeAddress()
    {
        $history = Middleware::history($this->container);
        $mock = new MockHandler([
            new Response(422, [], "{ \"error\": \"CANT_GEOCODE_ADDRESS\", \"message\": \"The address can't be geocoded\"}")
        ]);
        $handler = HandlerStack::create($mock);
        $handler->push($history);

        $client = new Client(['handler' => $handler]);
        $httpClient = new HttpClient($this->authenticator, $client);

        return $httpClient;
    }
}
