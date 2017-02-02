<?php

namespace Stuart\Infrastructure;

class ApiResponseFactory
{
    public static function fromGuzzleHttpResponse($guzzleHttpResponse)
    {
        $statusCode = $guzzleHttpResponse->getStatusCode();
        $body = json_decode($guzzleHttpResponse->getBody()->getContents());
        return new ApiResponse($statusCode, $body);
    }
}