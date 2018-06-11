<?php

namespace Stuart\Constant;

class StuartConstant
{
    const DATE_FORMAT = "Y-m-d\TH:i:s.uO";

    const TYPE_DROPOFF = 'dropoff';
    const TYPE_PICKUP = 'pickup';

    public static function getTypes()
    {
        return [
            self::TYPE_DROPOFF,
            self::TYPE_PICKUP,
        ];
    }
}
