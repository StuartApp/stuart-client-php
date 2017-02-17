<?php

namespace Stuart\Infrastructure;

use GuzzleHttp\Client;
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
     */
    public function __construct($authenticator)
    {
        $this->client = new Client();
        $this->authenticator = $authenticator;
        $this->baseUrl = $authenticator->getEnvironment()['base_url'];
    }


    public function performPost($formParams, $resource)
    {
        try {
            $response = $this->client->request('POST', $this->baseUrl . $resource, [
                'form_params' => $formParams,
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
            'User-Agent' => 'stuart-php-client/1.1.0'
        ];
    }

    private function handleRequestException(RequestException $e)
    {
        if ($e->hasResponse()) {
            $errorResponse = json_decode($e->getResponse()->getBody()->getContents());
            $errors = array();
            foreach ($errorResponse->errors as $error) {
                $errors[] = $error;
            }
            throw new ClientException($errors);
        } else {
            throw $e;
        }
    }
}
