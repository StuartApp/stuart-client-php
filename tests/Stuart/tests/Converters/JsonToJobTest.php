<?php

namespace Stuart\Tests\Converters;

use \Stuart\Tests\Mock;
use \Stuart\Converters\JsonToJob;

class JsonToJobTest extends \PHPUnit_Framework_TestCase
{
    private $mock;

    public function setUp()
    {
        $this->mock = new Mock();
    }

    public function test_it_produces_expected_pickups_and_drop_offs()
    {
        $job = $this->mock->job();
        $job->link($job->getPickups()[0], $job->getDropOffs()[0])
            ->setId($this->mock->delivery_id)
            ->setStatus($this->mock->delivery_status)
            ->setTrackingUrl($this->mock->delivery_tracking_url);

        self::assertEquals(
            JsonToJob::convert($this->mock->job_creation_response_json()),
            $job
        );
    }
}
