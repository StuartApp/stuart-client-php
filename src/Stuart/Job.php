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
    public function __construct($origin, $destination, $packageSize)
    {
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
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
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