<?php

namespace Stuart;

class Job
{
    private $id;
    private $origin;
    private $destination;
    private $packageSize;

    /**
     * Job constructor.
     * @param $id
     * @param $origin
     * @param $destination
     * @param $packageSize
     */
    public function __construct($id, $origin, $destination, $packageSize)
    {
        $this->id = $id;
        $this->origin = $origin;
        $this->destination = $destination;
        $this->packageSize = $packageSize;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
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