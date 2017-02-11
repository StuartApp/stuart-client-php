<?php

namespace Stuart\Tests;

use Stuart\Infrastructure\ApiResponse;

class ApiResponseTest extends \PHPUnit\Framework\TestCase
{

    public function test_it_should_not_be_successful()
    {
        // given
        $apiResponse = new ApiResponse(null, self::any());

        // when
        $success = $apiResponse->success();

        // then
        self::assertFalse($success);
    }

    public function test_it_should_be_successful()
    {
        // given
        $apiResponse = new ApiResponse(200, self::any());

        // when
        $success = $apiResponse->success();

        // then
        self::assertTrue($success);
    }
}
