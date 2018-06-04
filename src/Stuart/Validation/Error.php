<?php

namespace Stuart\Validation;

class Error
{
    private $key;

    public function __construct($key)
    {
        $this->key = $key;
    }

    public function getKey()
    {
        return $this->key;
    }

    public function __toString()
    {
        return $this->key;
    }
}
