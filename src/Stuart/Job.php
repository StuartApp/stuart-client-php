<?php

namespace Stuart;

class Job
{
    private $origin;
    private $destination;
    private $packageSize;

    /**
     * Job constructor.
     * @param $origin
     * @param $destination
     * @param $packageSize
     */
    public function __construct($origin, $destination, $packageSize)
    {
        $this->origin = $origin;
        $this->destination = $destination;
        $this->packageSize = $packageSize;
    }

    /**
     * @return mixed
     */
    public function getOrigin()
    {
        return $this->origin;
    }

    /**
     * @return mixed
     */
    public function getDestination()
    {
        return $this->destination;
    }

    /**
     * @return mixed
     */
    public function getPackageSize()
    {
        return $this->packageSize;
    }
}