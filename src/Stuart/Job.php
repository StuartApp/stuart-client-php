<?php

namespace Stuart;

class Job
{
    private $id;
    private $origin;
    private $destination;
    private $packageSize;
    private $trackingUrl;

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

    public function enrich($args)
    {
        $this->id = $args['id'];
        $this->trackingUrl = $args['tracking_url'];
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

    /**
     * @return mixed
     */
    public function getTrackingUrl()
    {
        return $this->trackingUrl;
    }
}