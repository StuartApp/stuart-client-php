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
     * @var bool
     */
    private $debugLogs;

    /**
     * HttpClient constructor.
     * @param $authenticator
     * @param $client
     */
    public function __construct($authenticator, $client = null, $debugLogs = false)
    {
        $this->authenticator = $authenticator;
        $this->baseUrl = $authenticator->getEnvironment()['base_url'];
        $this->client = $client === null ? new Client() : $client;
    }

    /**
     * @param $body
     * @param $resource
     * @param bool $isRetry Set to true if this method call is a retry for a previous auth failure
     * @return ApiResponse
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function performPost($body, $resource, $isRetry = false)
    {
        try {
            $response = $this->client->request('POST', $this->baseUrl . $resource, [
                'body' => $body,
                'headers' => $this->defaultHeaders()
            ]);
        } catch (RequestException $e) {
            if ($e->hasResponse()) {
                $response = $e->getResponse();
                if (!$isRetry && $response->getStatusCode() == 401 && $this->authenticator->accessTokenIsCachable()) {
                    if ($this->debugLogs) print "Token cached is expired. Getting a token...\n";
                    
                    $this->authenticator->getNewAccessToken();
                    // During peak hours Stuart's authentication replicas lag for 1-2 seconds.
                    // There's a chance that the newly created token does not exist yet in the replicas.
                    // This, even ugly, will only be happening when when token expires, which ATM is once per month.
                    sleep(2);
                    return $this->performPost($body, $resource, true);
                } else {
                    return $response;
                }
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
            'User-Agent' => 'stuart-php-client/3.6.10',
            'Content-Type' => 'application/json'
        ];
    }

    /**
     * @param $resource
     * @param $query
     * @return ApiResponse
     */
    public function performGet($resource, $query = [], $isRetry = false)
    {
        try {
            $response = $this->client->request('GET', $this->baseUrl . $resource, [
                'headers' => $this->defaultHeaders(),
                'query' => $query
            ]);
        } catch (RequestException $e) {
            if ($e->hasResponse()) {
                $response = $e->getResponse();
                if (!$isRetry && $response->getStatusCode() == 401 && $this->authenticator->accessTokenIsCachable()) {
                    if ($this->debugLogs) print "Token cached is expired. Getting a token...\n";
                    $this->authenticator->getNewAccessToken();
                    // During peak hours Stuart's authentication replicas lag for 1-2 seconds.
                    // There's a chance that the newly created token does not exist yet in the replicas.
                    // This, even ugly, will only be happening when when token expires, which ATM is once per month.
                    sleep(2);
                    return $this->performGet($resource, $query, true);
                } else {
                    return $response;
                }
            } else {
                throw $e;
            }
        }

        return ApiResponseFactory::fromGuzzleHttpResponse($response);
    }
}
