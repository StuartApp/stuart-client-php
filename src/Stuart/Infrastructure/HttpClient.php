<?php

namespace Stuart\Infrastructure;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Stuart\ClientError;

class HttpClient
{
    /**
     * @var \Stuart\Infrastructure\Authenticator
     */
    private $authenticator;
    /**
     * @var Client
     */
    private $client;
    /**
     * @var string
     */
    private $baseUrl;

    /**
     * HttpClient constructor.
     * @param $authenticator
     * @param $client
     */
    public function __construct($authenticator, $client = null)
    {
        $this->authenticator = $authenticator;
        $this->baseUrl = $authenticator->getEnvironment()['base_url'];
        $this->client = $client === null ? new Client() : $client;
    }

    /**
     * @param $body
     * @param $resource
     * @return ApiResponse
     */
    public function performPost($body, $resource)
    {
        try {
            $response = $this->client->request('POST', $this->baseUrl . $resource, [
                'body' => $body,
                'headers' => $this->defaultHeaders()
            ]);
        } catch (RequestException $e) {
            if ($e->hasResponse()) {
                $response = $e->getResponse();
            } else {
                throw $e;
            }
        }

        return ApiResponseFactory::fromGuzzleHttpResponse($response);
    }

    /**
     * @return array
     */
    private function defaultHeaders()
    {
        return [
            'Authorization' => 'Bearer ' . $this->authenticator->getAccessToken(),
            'User-Agent' => 'stuart-php-client/3.1.0',
            'Content-Type' => 'application/json'
        ];
    }

    /**
     * @param $resource
     * @param $query
     * @return ApiResponse
     */
    public function performGet($resource, $query = [])
    {
        try {
            $response = $this->client->request('GET', $this->baseUrl . $resource, [
                'headers' => $this->defaultHeaders(),
                'query' => $query
            ]);
        } catch (RequestException $e) {
            if ($e->hasResponse()) {
                $response = $e->getResponse();
            } else {
                throw $e;
            }
        }

        return ApiResponseFactory::fromGuzzleHttpResponse($response);
    }
}
