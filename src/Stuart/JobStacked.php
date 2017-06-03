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
     * @return string
     */
    public function toJson()
    {
        $result = array(
            'job' => array()
        );

        if (count($this->pickups) === 1 && $this->pickups[0]->getPickupAt() != null) {
            $result['job']['pickup_at'] = $this->pickups[0]->getPickupAt()->format(\DateTime::ATOM);
        }

        if (count($this->dropOffs) === 1 && $this->dropOffs[0]->getDropOffAt() != null) {
            $result['job']['dropoff_at'] = $this->dropOffs[0]->getDropOffAt()->format(\DateTime::ATOM);
        }

        $pickups = array();
        foreach ($this->pickups as $pickup) {
            $pickups[] = $this->locationAsArray($pickup);
        }

        $dropOffs = array();
        foreach ($this->dropOffs as $dropOff) {
            $dropOffs[] = array_merge($this->locationAsArray($dropOff), array(
                'package_type' => $dropOff->getPackageType(),
                'package_description' => $dropOff->getPackageDescription(),
                'client_reference' => $dropOff->getClientReference()
            ));
        }

        $result['job']['pickups'] = $pickups;

        $result['job']['dropoffs'] = $dropOffs;

        return json_encode($result);
    }


    private function locationAsArray($location)
    {
        return array(
            'address' => $location->getAddress(),
            'comment' => $location->getComment(),
            'contact' => array(
                'firstname' => $location->getFirstName(),
                'lastname' => $location->getLastName(),
                'phone' => $location->getPhone(),
                'company' => $location->getCompany()
            )
        );
    }
}
