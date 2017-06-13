<?php

namespace Stuart\Infrastructure;

class ApiResponseFactory
{
    public static function fromGuzzleHttpResponse($guzzleHttpResponse)
    {
        $statusCode = $guzzleHttpResponse->getStatusCode();
        $body = $guzzleHttpResponse->getBody()->getContents();
        return new ApiResponse($statusCode, $body);
    }
}
