<?php

namespace Stuart\Tests\Infrastructure;

use Desarrolla2\Cache\Cache;
use League\OAuth2\Client\Token\AccessToken;

use Stuart\Infrastructure\Authenticator;
use Stuart\Infrastructure\Environment;

class AuthenticatorTestTest extends \PHPUnit_Framework_TestCase
{
    public function test_it_reuse_access_token_when_cache_given()
    {
        // given
        $adapter = new InMemoryAdapter();
        $cache = new Cache($adapter);
        $accessToken = new AccessToken(['access_token' => 'sample-access-token', 'expires' => '1920806443']);
        $cache->set('STUART_SANDBOX_CACHE_ACCESS_TOKEN_KEY', $accessToken);
        $authenticator = new Authenticator(
            Environment::SANDBOX, 'sample_client_id', 'sample_client_secret', $cache
        );

        // when
        $accessToken1 = $authenticator->getAccessToken();
        $accessToken2 = $authenticator->getAccessToken();

        // then
        self::assertEquals($accessToken1, $accessToken2);
    }
}
