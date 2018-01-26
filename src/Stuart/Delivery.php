<?php

namespace Stuart;

class Delivery
{
    private $id;

    /**
     * @var string
     */
    private $status;

    private $trackingUrl;

    /**
     * @var Location
     */
    private $origin;
    /**
     * @var Location
     */
    private $destination;

    /**
     * Delivery constructor.
     * @param Location $origin
     * @param Location $destination
     */
    public function __construct($origin, $destination)
    {
        $this->origin = $origin;
        $this->destination = $destination;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param $status
     *
     * @return self
     */
    public function setStatus($status)
    {
        $this->status = $status;
        return $this;
    }

    public function getTrackingUrl()
    {
        return $this->trackingUrl;
    }

    public function setTrackingUrl($trackingUrl)
    {
        $this->trackingUrl = $trackingUrl;
        return $this;
    }

    /**
     * @return Location
     */
    public function getOrigin()
    {
        return $this->origin;
    }

    /**
     * @return Location
     */
    public function getDestination()
    {
        return $this->destination;
    }
}
