<?php

namespace Stuart\Tests\Converters;

use Stuart\Converters\JobToJson;
use Stuart\Tests\Mock;

class JobToJsonTest extends \PHPUnit_Framework_TestCase
{
    private $mock;

    public function setUp()
    {
        $this->mock = new Mock();
    }

    public function test_it_produces_expected_json()
    {
        self::assertEquals(
            JobToJson::convert($this->mock->job()),
            $this->mock->job_request_json()
        );
    }
}
