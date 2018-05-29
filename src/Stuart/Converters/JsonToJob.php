<?php

namespace Stuart\Converters;

use Stuart\Job;

class JsonToJob
{
    public static $STUART_DATE_FORMAT = "Y-m-d\TH:i:s.uO";

    /**
     * Converts a JSON formatted Stuart Job into a Job object.
     *
     * @param String $json
     * @return Job
     */
    public static function convert($json)
    {
        $body = json_decode($json);
        $job = new Job();

        $job->setId($body->id);
        $job->setTransportType(isset($body->transport_type) ? $body->transport_type : null);
        $job->setAssignmentCode(isset($body->assignment_code) ? $body->assignment_code : null);
        $job->setStatus($body->status);
        $job->setDistance($body->distance);
        $job->setDuration($body->duration);

        foreach ($body->deliveries as $delivery) {
            $job
                ->link(
                    $job->addPickup(self::fullTextAddress($delivery->pickup->address))
                        ->setPickupAt(\DateTime::createFromFormat(self::$STUART_DATE_FORMAT, $body->pickup_at))
                        ->setComment($delivery->pickup->comment)
                        ->setContactCompany($delivery->pickup->contact->company_name)
                        ->setContactFirstName($delivery->pickup->contact->firstname)
                        ->setContactLastName($delivery->pickup->contact->lastname)
                        ->setContactPhone($delivery->pickup->contact->phone)
                        ->setContactEmail($delivery->pickup->contact->email),
                    $job->addDropOff(self::fullTextAddress($delivery->dropoff->address))
                        ->setDropoffAt(\DateTime::createFromFormat(self::$STUART_DATE_FORMAT, $body->dropoff_at))
                        ->setPackageType($delivery->package_type)
                        ->setPackageDescription($delivery->package_description)
                        ->setClientReference($delivery->client_reference)
                        ->setComment($delivery->dropoff->comment)
                        ->setContactCompany($delivery->dropoff->contact->company_name)
                        ->setContactFirstName($delivery->dropoff->contact->firstname)
                        ->setContactLastName($delivery->dropoff->contact->lastname)
                        ->setContactPhone($delivery->dropoff->contact->phone)
                        ->setContactEmail($delivery->dropoff->contact->email)
                )
                ->setId($delivery->id)
                ->setStatus($delivery->status)
                ->setTrackingUrl($delivery->tracking_url);
        }

        return $job;
    }

    /**
     * @param $address
     *
     * @return string
     */
    private static function fullTextAddress($address)
    {
        return implode(', ', (array)$address);
    }
}
