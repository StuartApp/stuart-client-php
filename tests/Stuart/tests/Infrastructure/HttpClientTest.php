<?php

namespace Stuart\Tests;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Response;
use Stuart\Infrastructure\Environment;

use Stuart\Infrastructure\HttpClient;
use Stuart\Infrastructure\Authenticator;

class HttpClientTest extends \PHPUnit_Framework_TestCase
{
    private $authenticator;

    private $container;

    const PHP_CLIENT_USER_AGENT = 'stuart-php-client/1.0.0';

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
        $httpClient = $this->getNewHttpContainer();
        $httpClient->performGet('/test');

        foreach ($this->container as $transaction) {
            $userAgent = $transaction['request']->getHeaders()['User-Agent'][0];
            self::assertEquals(self::PHP_CLIENT_USER_AGENT, $userAgent);
        }
    }

    public function test_it_sends_the_php_lib_version_header_on_post()
    {
        $httpClient = $this->getNewHttpContainer();
        $httpClient->performPost([], '/test');

        foreach ($this->container as $transaction) {
            $userAgent = $transaction['request']->getHeaders()['User-Agent'][0];
            self::assertEquals(self::PHP_CLIENT_USER_AGENT, $userAgent);
        }
    }

    public function test_it_get_with_the_proper_parameters()
    {
        $httpClient = $this->getNewHttpContainer();
        $httpClient->performGet('/test');

        foreach ($this->container as $transaction) {
            self::assertEquals('GET', $transaction['request']->getMethod());
            self::assertEquals('/test', $transaction['request']->getUri()->getPath());
        }
    }

    private function getNewHttpContainer()
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
}
