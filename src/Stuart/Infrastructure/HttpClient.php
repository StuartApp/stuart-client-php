<?php

namespace Stuart\Infrastructure;

use GuzzleHttp\Client;

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

    /**
     * @param $formParams
     * @param $resource
     * @return ApiResponse
     */
    public function performPost($formParams, $resource)
    {
        try {
            $response = $this->client->request('POST', $this->baseUrl . $resource, [
                'form_params' => $formParams,
                'headers' => $this->defaultHeaders()
            ]);
        } catch (\Exception $e) {
            return new ApiResponse(null, null);
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
        } catch (\Exception $e) {
            return new ApiResponse(null, null);
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
}
