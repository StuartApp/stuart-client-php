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

    public function toJson()
    {
        $pickups = array();
        foreach ($this->pickups as $pickup) {
            $pickups[] = $pickup->toArray();
        }

        $dropOffs = array();
        foreach ($this->dropOffs as $dropOff) {
            $dropOffs[] = $dropOff->toArray();
        }

        return json_encode(
            array(
                'job' => array(
                    'pickups' => $pickups,
                    'dropoffs' => $dropOffs
                )
            )
        );
    }
}