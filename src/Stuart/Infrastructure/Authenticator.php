<?php

namespace Stuart\Infrastructure;

use \League\OAuth2\Client\Provider\GenericProvider;

class Authenticator
{

    private $provider;
    private $environment;

    /**
     * Authenticator constructor.
     * @param $environment
     * @param $api_client_id
     * @param $api_client_secret
     */
    public function __construct($environment, $api_client_id, $api_client_secret)
    {
        $base_url = $environment['base_url'];
        $this->environment = $environment;
        $this->provider = new GenericProvider([
            'clientId' => $api_client_id,
            'clientSecret' => $api_client_secret,
            'urlAccessToken' => $base_url . '/oauth/token',
            'redirectUri' => $base_url,
            'urlAuthorize' => $base_url . '/oauth/authorize',
            'urlResourceOwnerDetails' => $base_url . '/oauth/resource'
        ]);
    }

    /**
     * @return \League\OAuth2\Client\Token\AccessToken
     */
    public function getAccessToken()
    {
        return $this->provider->getAccessToken('client_credentials');
    }

    /**
     * @return \Stuart\Infrastructure\Environment
     */
    public function getEnvironment()
    {
        return $this->environment;
    }
}
