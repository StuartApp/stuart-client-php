<?php

namespace Stuart;

class Pickup extends Location
{
    private $pickupAt;

    public function setPickupAt($pickupAt)
    {
        $this->pickupAt = $pickupAt;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPickupAt()
    {
        return $this->pickupAt;
    }
}