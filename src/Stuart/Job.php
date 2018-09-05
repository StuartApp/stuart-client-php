<?php

namespace Stuart;

use Stuart\converters\JsonToJob;

class Job
{
    private $id;
    private $transportType;
    private $assignmentCode;
    private $status;
    private $pickups = array();
    private $dropOffs = array();
    private $deliveries = array();
    private $distance;
    private $duration;

    /**
     * @var Pricing
     */
    private $pricing;

    /**
     * @param $address
     * @return \Stuart\Pickup
     */
    public function addPickup($address)
    {
        $pickup = new Pickup();
        $pickup->setAddress($address);
        $this->pickups[] = $pickup;
        return $pickup;
    }

    /**
     * @param $address
     * @return DropOff
     */
    public function addDropOff($address)
    {
        $dropOff = new DropOff();
        $dropOff->setAddress($address);
        $this->dropOffs[] = $dropOff;
        return $dropOff;
    }

    /**
     * This method allows you to create deliveries from two locations. It's only used by the
     * JsonToJob converter, you cannot create you own route.
     *
     * @param Location $origin
     * @param Location $destination
     * @return Delivery
     *
     * @see JsonToJob
     */
    public function link($origin, $destination)
    {
        $delivery = new Delivery($origin, $destination);
        $this->deliveries[] = $delivery;
        return $delivery;
    }

    /**
     * @return Pickup[]
     */
    public function getPickups()
    {
        return $this->pickups;
    }

    /**
     * @return DropOff[]
     */
    public function getDropOffs()
    {
        return $this->dropOffs;
    }

    /**
     * @return Delivery[]
     */
    public function getDeliveries()
    {
        return $this->deliveries;
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
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param mixed $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * @return mixed
     */
    public function getTransportType()
    {
        return $this->transportType;
    }

    /**
     * @param mixed $transportType
     */
    public function setTransportType($transportType)
    {
        $this->transportType = $transportType;
    }

    /**
     * @return string
     */
    public function getAssignmentCode()
    {
        return $this->assignmentCode;
    }

    /**
     * @param string $assignmentCode
     */
    public function setAssignmentCode($assignmentCode)
    {
        $this->assignmentCode = $assignmentCode;
    }

    /**
     * Total Job distance, in kilometer.
     *
     * @return float
     */
    public function getDistance()
    {
        return $this->distance;
    }

    /**
     * @param float $distance
     */
    public function setDistance($distance)
    {
        $this->distance = $distance;
    }

    /**
     * Total Job duration estimation, in minute.
     *
     * @return int
     */
    public function getDuration()
    {
        return $this->duration;
    }

    /**
     * @param int $duration
     */
    public function setDuration($duration)
    {
        $this->duration = $duration;
    }

    /**
     * @return Pricing
     */
    public function getPricing()
    {
        return $this->pricing;
    }

    /**
     * @param Pricing $pricing
     */
    public function setPricing($pricing)
    {
        $this->pricing = $pricing;
    }
}
