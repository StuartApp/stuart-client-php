<?php

namespace Stuart\Infrastructure;

class Authenticator
{

    private $provider;

    /**
     * Authenticator constructor.
     * @param $base_url
     * @param $api_client_id
     * @param $api_client_secret
     */
    public function __construct($base_url, $api_client_id, $api_client_secret)
    {
        $this->provider = new \League\OAuth2\Client\Provider\GenericProvider([
            'clientId' => $api_client_id,
            'clientSecret' => $api_client_secret,
            'urlAccessToken' => $base_url . '/oauth/token',
            'redirectUri' => $base_url,
            'urlAuthorize' => $base_url . '/oauth/authorize',
            'urlResourceOwnerDetails' => $base_url . '/oauth/resource'
        ]);
    }

    public function accessToken()
    {
        return $this->provider->getAccessToken('client_credentials');
    }
}
