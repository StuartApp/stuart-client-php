<?php

namespace Stuart\Infrastructure;

use GuzzleHttp\Exception\RequestException;
use Stuart\ClientError;
use Stuart\ClientException;

class HttpClient
{
    /**
     * @var \Stuart\Infrastructure\Authenticator
     */
    private $authenticator;
    /**
     * @var \GuzzleHttp\Client
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
    public function __construct($authenticator, $client)
    {
        $this->authenticator = $authenticator;
        $this->baseUrl = $authenticator->getEnvironment()['base_url'];
        $this->client = $client;
    }


    /**
     * @param $formParams
     * @param $resource
     * @return ApiResponse
     */
    public function performPost($formParams, $resource)
    {
        try {
            $response = $this->client->request('POST', $this->baseUrl . $resource, [
                'body' => $formParams,
                'headers' => $this->defaultHeaders()
            ]);
        } catch (RequestException $e) {
            $this->handleRequestException($e);
        }

        return ApiResponseFactory::fromGuzzleHttpResponse($response);
    }

    /**
     * @param $resource
     * @return ApiResponse
     */
    public function performGet($resource)
    {
        try {
            $response = $this->client->request('GET', $this->baseUrl . $resource, [
                'headers' => $this->defaultHeaders()
            ]);
        } catch (RequestException $e) {
            $this->handleRequestException($e);
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
            'User-Agent' => 'stuart-php-client/2.0.0',
            'Content-Type' => 'application/json'
        ];
    }

    private function handleRequestException(RequestException $e)
    {
        if ($e->hasResponse()) {
            $errorResponse = json_decode($e->getResponse()->getBody()->getContents());
            $errors = array();
            if (isset($errorResponse->errors)) {
                foreach ($errorResponse->errors as $error) {
                    $errors[] = $error;
                }
            }
            throw new ClientException($errors);
        } else {
            throw $e;
        }
    }
}
