<?php

namespace Stuart;

class Job
{
    private $id;
    private $origin;
    private $destination;
    private $packageSize;
    private $clientReference;
    private $pickupAt;
    private $trackingUrl;

    /**
     * Job constructor.
     * @param $origin
     * @param $destination
     * @param $packageSize
     * @param $options
     */
    public function __construct($origin, $destination, $packageSize, array $options = array())
    {
        $this->origin = $origin;
        $this->destination = $destination;
        $this->packageSize = $packageSize;
        $this->clientReference = $options['client_reference'];
        $this->pickupAt = $options['pickup_at'];
    }

    public function enrich($args)
    {
        $this->id = $args['id'];
        $this->trackingUrl = $args['tracking_url'];
    }

    /**
     * @return \DateTime
     */
    public function getPickupAt()
    {
        return $this->pickupAt;
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
    public function getClientReference()
    {
        return $this->clientReference;
    }

    /**
     * @return mixed
     */
    public function getTrackingUrl()
    {
        return $this->trackingUrl;
    }
}
