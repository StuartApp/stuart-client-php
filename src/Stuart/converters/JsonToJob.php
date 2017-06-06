<?php

namespace Stuart\converters;

use Stuart\Job;

class JsonToJob
{
    /**
     * Converts a JSON formatted Stuart Job into a Job object.
     *
     * @param String $json
     * @return Job
     */
    public static function convert($json)
    {
        $bodyAsArray = json_decode($json);

        $job = new Job();

        foreach ($bodyAsArray->deliveries as $delivery) {
            $job->addPickup(self::fullTextAddress($delivery->pickup->address))
                ->setPickupAt(\DateTime::createFromFormat(\DateTime::ATOM, $bodyAsArray->pickup_at))
                ->setComment($delivery->pickup->comment)
                ->setContactCompany($delivery->pickup->contact->company_name)
                ->setContactFirstName($delivery->pickup->contact->firstname)
                ->setContactLastName($delivery->pickup->contact->lastname)
                ->setContactPhone($delivery->pickup->contact->phone);

            $job->addDropOff(self::fullTextAddress($delivery->dropoff->address))
                ->setDropOffAt(\DateTime::createFromFormat(\DateTime::ATOM, $bodyAsArray->dropoff_at))
                ->setPackageType($delivery->package_type)
                ->setPackageDescription($delivery->package_description)
                ->setClientReference($delivery->client_reference)
                ->setComment($delivery->dropoff->comment)
                ->setContactCompany($delivery->dropoff->contact->company_name)
                ->setContactFirstName($delivery->dropoff->contact->firstname)
                ->setContactLastName($delivery->dropoff->contact->lastname)
                ->setContactPhone($delivery->dropoff->contact->phone);
        }

        return $job;
    }

    private static function fullTextAddress($address)
    {
        return implode(', ', (array)$address);
    }
}
