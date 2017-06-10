<?php

namespace Stuart\Tests;

class JobTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Mock
     */
    private $mock;

    public function setUp()
    {
        $this->mock = new Mock();
    }


    public function test_it_has_no_route_when_no_link()
    {
        self::assertFalse($this->mock->job_(1, 1)
            ->hasRoute());
    }

    public function test_it_has_a_route_when_one_link()
    {
        $job = $this->mock->job_(1, 1);
        $job->link($job->getPickups()[0], $job->getDropOffs()[0]);
        self::assertTrue($job->hasRoute());
    }

    public function test_it_has_a_route_when_5_link()
    {
        $job = $this->mock->job_(1, 5);
        $job->link($job->getPickups()[0], $job->getDropOffs()[0]);
        $job->link($job->getDropOffs()[0], $job->getDropOffs()[1]);
        $job->link($job->getDropOffs()[1], $job->getDropOffs()[2]);
        $job->link($job->getDropOffs()[2], $job->getDropOffs()[1]);
        $job->link($job->getDropOffs()[4], $job->getDropOffs()[4]);
        self::assertTrue($job->hasRoute());
    }
}
