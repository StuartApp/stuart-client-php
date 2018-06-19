<?php

namespace Stuart;

class SchedulingSlots
{
    /**
     * @var string pickup|dropoff
     */
    private $type;

    private $slots = [];

    public function __construct($type)
    {
        $this->type = $type;
    }

    public function getSlots()
    {
        return $this->slots;
    }

    public function addSlot(\DateTime $start, \DateTime $end)
    {
        $this->slots[] = [
            'start' => $start,
            'end' => $end,
        ];
    }

    public function setSlots($slots)
    {
        $this->slots = [];

        foreach ($slots as $slot) {
            $this->addSlot($slot['start'], $slot['end']);
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }
}
