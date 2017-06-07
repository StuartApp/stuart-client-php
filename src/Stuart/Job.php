<?php

namespace Stuart;

class Job
{
    private $id;
    private $status;
    private $pickups = array();
    private $dropOffs = array();

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
}
