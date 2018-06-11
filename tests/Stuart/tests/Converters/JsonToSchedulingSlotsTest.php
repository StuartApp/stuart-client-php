<?php

namespace Stuart\Tests\Converters;

use Stuart\Converters\JsonToSchedulingSlots;
use Stuart\Tests\Mock;

class JsonToSchedulingSlotsTest extends \PHPUnit_Framework_TestCase
{
    private $mock;

    public function setUp()
    {
        $this->mock = new Mock();
    }

    public function test_it_produces_expected_pickups_and_drop_offs()
    {
        $schedulingSlots = JsonToSchedulingSlots::convert($this->mock->scheduling_slots_response_json());

        self::assertEquals('London', $schedulingSlots->getZone()->getName());
        self::assertEquals(\DateTime::createFromFormat('Y-m-d\TH:i:s.uO', '2017-07-20T08:45:00.000+01:00'), $schedulingSlots->getSlots()[0]['start']);
    }
}
