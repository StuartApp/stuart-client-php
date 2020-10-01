<?php

namespace Stuart\Infrastructure;

class StuartAccessToken extends \League\OAuth2\Client\Token\AccessToken
{
    public function hasExpired()
    {
        // Ideally we should check the expiration field but we're fine letting server check that on our user's behalf.
        return false;
    }

}