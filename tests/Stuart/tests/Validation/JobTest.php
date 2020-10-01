<?php

namespace Stuart\Tests;


class JobTest extends \PHPUnit\Framework\TestCase
{

    private $mock;

    public function setUp()
    {
        $this->mock = new Mock();
    }

    public function test_should_return_an_error_when_dropoff_at_is_used_with_several_dropoffs()
    {
        // given
        $job = $this->mock->job();
        $job->getDropoffs()[0]->setDropoffAt($this->mock->dropoff_at());
        $job->addDropOff('some-address');

        // when
        $jobValidation = new \Stuart\Validation\Job();
        $errors = $jobValidation->validate($job);

        // then
        self::assertEquals($errors[0]->getKey(), 'DROPOFF_AT_CAN_BE_USED_WITH_ONLY_ONE_DROPOFF');
    }
}
