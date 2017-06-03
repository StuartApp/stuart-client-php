<?php

namespace Stuart;

class JobStacked
{
    private $pickups = array();
    private $dropOffs = array();

    /**
     * @param $address
     * @return Pickup
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
}
