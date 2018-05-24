<?php

namespace Stuart\Routing;

class GraphHopper
{

    public function findRound($pickup, $dropoffs)
    {

        $computedDropoffs = [];
        $wastedDropoffs = [];
        return (object)[$pickup, $computedDropoffs, $wastedDropoffs];
    }

    public function validateDropoffs($dropoffs)
    {
        // cannot have pickup at on the pikcup

    }

    public function getRequest($pickup, $dropoffs)
    {
        $result = array();

        // vehicles
        $vehicles = array();
        $vehicles[] = array(
            'vehicle_id' => '0001',
            'start_address' => $this->getAddress($pickup)
        );
        $result['vehicles'] = $vehicles;

        // services
        $services = array();

        $services[] = array(
            'id' => $pickup->getAddress(),
            'address' => $this->getAddress($pickup)
        );

        foreach ($dropoffs as $dropoff) {
            $services[] = array(
                'id' => $dropoff->getAddress(),
                'address' => $this->getAddress($dropoff)
            );
        }
        $result['services'] = $services;

        return $result;
    }

    private function getAddress($location)
    {
        $geoloc = $this->geocode($location->getAddress());
        return array(
            'location_id' => $location->getAddress(),
            'lat' => $geoloc->lat,
            'lon' => $geoloc->lon
        );
    }

    private function geocode($fullTextAddress)
    {
        return (object)array('lat' => 0.123, 'lon' => 2.1234);
    }
}

