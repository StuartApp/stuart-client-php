<?php

namespace Stuart\Infrastructure;

class HttpClient
{
    private $provider;
    private $baseUrl;

    public function __construct($useSandbox, $api_client_id, $api_client_secret)
    {
        $baseUrl = $this->baseUrl($useSandbox);
        $this->provider = new \League\OAuth2\Client\Provider\GenericProvider([
            'clientId' => $api_client_id,
            'clientSecret' => $api_client_secret,
            'urlAccessToken' => $baseUrl . '/oauth/token',
            'redirectUri' => $baseUrl,
            'urlAuthorize' => $baseUrl . '/oauth/authorize',
            'urlResourceOwnerDetails' => $baseUrl . '/oauth/resource'
        ]);
        $this->baseUrl = $baseUrl;
    }

    public function performPost($formParams, $resource)
    {
        $client = $this->provider->getHttpClient();

        try {
            $response = $client->request('POST', $this->baseUrl . $resource, [
                'form_params' => $formParams,
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->accessToken()
                ]
            ]);
        } catch (Exception $e) {
            return new ApiResponse(null, null);
        }

        return ApiResponseFactory::fromGuzzleHttpResponse($response);
    }

    public function performGet($resource)
    {
        $client = $this->provider->getHttpClient();

        try {
            $response = $client->request('GET', $this->baseUrl . $resource, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->accessToken()
                ]
            ]);
        } catch (Exception $e) {
            return new ApiResponse(null, null);
        }

        return ApiResponseFactory::fromGuzzleHttpResponse($response);
    }

    private function accessToken()
    {
        return $this->provider->getAccessToken('client_credentials');
    }

    private function baseUrl($useSandbox)
    {
        if ($useSandbox) {
            return 'https://sandbox-api.stuart.com';
        } else {
            return 'https://api.stuart.com';
        }
    }
}
