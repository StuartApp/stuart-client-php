<?php

namespace Stuart\Infrastructure;

use GuzzleHttp\Client;

class HttpClient
{
    private $authenticator;
    private $baseUrl;

    public function __construct($useSandbox, $authenticator)
    {
        $this->baseUrl = $this->baseUrl($useSandbox);
        $this->client = new Client();
        $this->authenticator = $authenticator;
    }

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

    public function performGet($resource)
    {
        try {
            $response = $this->client->request('GET', $this->baseUrl . $resource, [
                'headers' => $this->defaultHeaders()
            ]);
        } catch (Exception $e) {
            return new ApiResponse(null, null);
        }

        return ApiResponseFactory::fromGuzzleHttpResponse($response);
    }

    private function baseUrl($useSandbox)
    {
        if ($useSandbox) {
            return 'https://sandbox-api.stuart.com';
        } else {
            return 'https://api.stuart.com';
        }
    }

    private function defaultHeaders()
    {
        return [
            'Authorization' => 'Bearer ' . $this->authenticator->accessToken(),
            'User-Agent' => 'stuart-php-client/1.1.0'
        ];
    }
}
