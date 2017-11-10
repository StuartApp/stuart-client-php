<?php

namespace Stuart;

class DropOff extends Location
{
    private $packageType;
    private $packageDescription;
    private $clientReference;

    public function setPackageType($packageType)
    {
        $this->packageType = $packageType;
        return $this;
    }

    public function setPackageDescription($packageDescription)
    {
        $this->packageDescription = $packageDescription;
        return $this;
    }

    public function setClientReference($clientReference)
    {
        $this->clientReference = $clientReference;
        return $this;
    }

    public function getPackageType()
    {
        return $this->packageType;
    }

    public function getPackageDescription()
    {
        return $this->packageDescription;
    }

    public function getClientReference()
    {
        return $this->clientReference;
    }
}
