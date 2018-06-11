<?php

namespace Stuart\Converters;

class JsonToSchedulingSlots
{
    public static function convert($json)
    {
        $body = json_decode($json);
        $zone = new \Stuart\Zone();
        $zone
            ->setId($body->zone->id)
            ->setRegionId($body->zone->region_id)
            ->setName($body->zone->name)
            ->setCode($body->zone->code)
            ->setTimezone($body->zone->timezone)
            ->setLatitude($body->zone->latitude)
            ->setLongitude($body->zone->longitude)
            ->setRoutesToAvoid($body->zone->routes_to_avoid)
            ->setShortCode($body->zone->short_code)
            ->setOpsMail($body->zone->ops_mail)
            ->setLocale($body->zone->locale)
        ;

        $schedulingSlots = new \Stuart\SchedulingSlots($body->type, $zone);

        foreach ($body->slots as $slot) {
            $schedulingSlots->addSlot(
                \DateTime::createFromFormat(\Stuart\Constant\StuartConstant::DATE_FORMAT, $slot->start_time),
                \DateTime::createFromFormat(\Stuart\Constant\StuartConstant::DATE_FORMAT, $slot->end_time)
            );
        }

        return $schedulingSlots;
    }
}
