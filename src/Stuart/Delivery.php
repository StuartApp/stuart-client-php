<?php

namespace Stuart;

class Delivery
{
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
