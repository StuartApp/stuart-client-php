<?php

namespace Stuart\Routing\GraphHopper;

class Formatter
{
    public static function convertToVehicles($locationId, $lat, $lon, $vehicleCount, $maxDropoffs, $maxDistance)
    {
        $vehicles = array();

        while ($vehicleCount > 0) {
            $vehicles[] = array(
                'vehicle_id' => '000' . $vehicleCount,
                'start_address' => self::convertToAddress($locationId, $lat, $lon),
                'return_to_depot' => false,
                'max_activities' => $maxDropoffs,
                'max_distance' => $maxDistance
            );
            $vehicleCount--;
        }

        return $vehicles;
    }

    public static function convertToAddress($locationId, $lat, $lng)
    {
        return array(
            'location_id' => $locationId,
            'lat' => $lat,
            'lon' => $lng
        );
    }
}
