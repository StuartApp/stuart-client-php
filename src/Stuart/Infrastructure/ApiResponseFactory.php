<?php

namespace Stuart\Infrastructure;

class ApiResponseFactory
{
    public static function fromGuzzleHttpResponse($guzzleHttpResponse)
    {
        $statusCode = $guzzleHttpResponse->getStatusCode();
        $body = (string)$guzzleHttpResponse->getBody();
        return new ApiResponse($statusCode, $body);
    }
}
