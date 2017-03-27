<?php

namespace Stuart\Infrastructure;

use \Desarrolla2\Cache\Cache;
use \League\OAuth2\Client\Provider\GenericProvider;

class Authenticator
{
    private $provider;
    private $environment;
    private $cache;

    /**
     * Authenticator constructor.
     * @param $environment
     * @param $api_client_id
     * @param $api_client_secret
     * @param $cache is a https://github.com/desarrolla2/Cache allowing you to cache an access token
     * for future re-use.
     */
    public function __construct($environment, $api_client_id, $api_client_secret, Cache $cache = null)
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
        $this->cache = $cache;
    }

    /**
     * @return \League\OAuth2\Client\Token\AccessToken
     */
    public function getAccessToken()
    {
        if ($this->accessTokenIsCachable()) {
            $accessTokenFromCache = $this->getAccessTokenFromCache();
            if ($accessTokenFromCache !== null && !$accessTokenFromCache->hasExpired()) {
                return $accessTokenFromCache;
            }
        }

        return $this->getNewAccessToken();
    }

    /**
     * @return \Stuart\Infrastructure\Environment
     */
    public function getEnvironment()
    {
        return $this->environment;
    }

    private function getNewAccessToken()
    {
        $accessToken = $this->provider->getAccessToken('client_credentials');
        if ($this->accessTokenIsCachable()) {
            $this->addAccessTokenToCache($accessToken);
        }
        return $accessToken;
    }

    private function accessTokenIsCachable()
    {
        return $this->cache !== null;
    }

    private function getAccessTokenFromCache()
    {
        return $this->cache->get($this->accessTokenCacheKey());
    }

    private function addAccessTokenToCache($accessToken)
    {
        $this->cache->set($this->accessTokenCacheKey(), $accessToken);
    }

    private function accessTokenCacheKey()
    {
        $envAsString = $this->environment === Environment::SANDBOX ? 'SANDBOX' : 'PRODUTION';
        return 'STUART_' . $envAsString . '_CACHE_ACCESS_TOKEN_KEY';
    }
}
