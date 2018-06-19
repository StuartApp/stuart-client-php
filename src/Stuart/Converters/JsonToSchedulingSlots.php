<?php

namespace Stuart\Converters;

class JsonToSchedulingSlots
{
    public static function convert($json)
    {
        $body = json_decode($json);

        $schedulingSlots = new \Stuart\SchedulingSlots($body->type);

        foreach ($body->slots as $slot) {
            $schedulingSlots->addSlot(
                \DateTime::createFromFormat(JsonToJob::$STUART_DATE_FORMAT, $slot->start_time),
                \DateTime::createFromFormat(JsonToJob::$STUART_DATE_FORMAT, $slot->end_time)
            );
        }

        return $schedulingSlots;
    }
}
