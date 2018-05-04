<?php

namespace Stuart\Infrastructure;

class ApiResponse
{
    private $statusCode;
    private $body;

    public function __construct($statusCode, $body)
    {
        $this->statusCode = $statusCode;
        $this->body = $body;
    }

    public function getBody()
    {
        return $this->body;
    }

    public function success()
    {
        return $this->statusCode >= 200 && $this->statusCode <= 299;
    }

    public function getStatusCode()
    {
        return $this->statusCode;
    }
}
