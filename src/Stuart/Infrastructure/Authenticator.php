<?php

namespace Stuart\Infrastructure;

use League\OAuth2\Client\Provider\GenericProvider;
use Psr\SimpleCache\CacheInterface;

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
    public function __construct($environment, $api_client_id, $api_client_secret, $cache = null)
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
        if ($cache instanceof CacheInterface) {
            $this->cache = $cache;
        }
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

    private function accessTokenIsCachable()
    {
        return $this->cache !== null;
    }

    private function getAccessTokenFromCache()
    {
        return $this->cache->get($this->accessTokenCacheKey());
    }

    private function accessTokenCacheKey()
    {
        $envAsString = $this->environment === Environment::SANDBOX ? 'SANDBOX' : 'PRODUCTION';
        return 'STUART_' . $envAsString . '_CACHE_ACCESS_TOKEN_KEY';
    }

    private function getNewAccessToken()
    {
        $accessToken = $this->provider->getAccessToken('client_credentials');
        if ($this->accessTokenIsCachable()) {
            $this->addAccessTokenToCache($accessToken);
        }
        return $accessToken;
    }

    private function addAccessTokenToCache($accessToken)
    {
        $this->cache->set($this->accessTokenCacheKey(), $accessToken);
    }

    /**
     * @return \Stuart\Infrastructure\Environment
     */
    public function getEnvironment()
    {
        return $this->environment;
    }
}
