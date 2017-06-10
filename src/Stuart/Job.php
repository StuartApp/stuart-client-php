<?php

namespace Stuart;

class Job
{
    private $id;
    private $status;
    private $pickups = array();
    private $dropOffs = array();
    private $deliveries = array();

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
     * @param Location $origin
     * @param Location $destination
     * @return Delivery
     */
    public function link($origin, $destination)
    {
        $delivery = new Delivery($origin, $destination);
        $this->deliveries[] = $delivery;
        return $this;
    }

    public function hasRoute()
    {
        $arrayObject = new \ArrayObject($this->deliveries);
        $iterator = $arrayObject->getIterator();

        return $this->hasRouteRec($iterator, $iterator->current());
    }

    private function hasRouteRec($deliveriesIterator, $currentDelivery)
    {
        if ($deliveriesIterator->count() === 0) {
            return false;
        }

        if (null !== $deliveriesIterator->current()) {
            if ($currentDelivery->getDestination() !== $deliveriesIterator->current()->getOrigin()) {
                return false;
            } else {
                $deliveriesIterator->next();
                return $this->hasRouteRec($deliveriesIterator, $deliveriesIterator->current());
            }
        } else {
            return true;
        }

        return false;
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
