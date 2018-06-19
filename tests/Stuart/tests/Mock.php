<?php

namespace Stuart\Tests;

use Stuart\Converters\JsonToJob;
use Stuart\Job;

class Mock
{
    public $id = '1234567';
    public $status = 'new';
    public $transport_type = 'bike';
    public $assignment_code = 'Order 123';
    public $distance = 3.966;
    public $duration = 18;

    public $pickup_address_street = '12 rue de rivoli';
    public $pickup_address_postcode = '75004';
    public $pickup_address_country = 'france';
    public $pickup_address_city = 'paris';
    public $pickup_comment = 'comment';
    public $pickup_contact_company = 'company';
    public $pickup_contact_first_name = 'firstname';
    public $pickup_contact_last_name = 'lastname';
    public $pickup_contact_phone = '837746';
    public $pickup_contact_email = 'lastname@company.com';
    public $drop_off_address_street = '148 rue de charenton';
    public $drop_off_address_postcode = '75012';
    public $drop_off_address_country = 'france';
    public $drop_off_address_city = 'paris';
    public $drop_off_comment = 'comment';
    public $drop_off_contact_company = 'company';
    public $drop_off_contact_first_name = 'firstname';
    public $drop_off_contact_last_name = 'lastname';
    public $drop_off_contact_phone = '837746';
    public $drop_off_contact_email = 'firstname@gmail.com';
    public $drop_off_client_reference = 'reference';
    public $drop_off_package_description = 'decription';
    public $drop_off_package_type = 'small';
    public $delivery_id = '7654321';
    public $delivery_tracking_url = 'https://my-tracking-url';
    public $delivery_status = 'pending';

    /**
     * @return Job
     */
    public function job()
    {
        $job = new Job();

        $job->setId($this->id);
        $job->setStatus($this->status);
        $job->setTransportType($this->transport_type);
        $job->setAssignmentCode($this->assignment_code);
        $job->setDistance($this->distance);
        $job->setDuration($this->duration);

        $job->addPickup($this->pickup_address())
            ->setPickupAt(\DateTime::createFromFormat(JsonToJob::$STUART_DATE_FORMAT, $this->pickup_at()))
            ->setComment($this->pickup_comment)
            ->setContactCompany($this->pickup_contact_company)
            ->setContactFirstName($this->pickup_contact_first_name)
            ->setContactLastName($this->pickup_contact_last_name)
            ->setContactPhone($this->pickup_contact_phone)
            ->setContactEmail($this->pickup_contact_email);

        $job->addDropOff($this->drop_off_address())
            ->setDropoffAt(\DateTime::createFromFormat(JsonToJob::$STUART_DATE_FORMAT, $this->dropoff_at()))
            ->setComment($this->drop_off_comment)
            ->setContactCompany($this->drop_off_contact_company)
            ->setContactFirstName($this->drop_off_contact_first_name)
            ->setContactLastName($this->drop_off_contact_last_name)
            ->setContactPhone($this->drop_off_contact_phone)
            ->setContactEmail($this->drop_off_contact_email)
            ->setClientReference($this->drop_off_client_reference)
            ->setPackageDescription($this->drop_off_package_description)
            ->setPackageType($this->drop_off_package_type);

        return $job;
    }

    public function pickup_address()
    {
        return $this->pickup_address_street
            . ', ' . $this->pickup_address_postcode
            . ', ' . $this->pickup_address_city
            . ', ' . $this->pickup_address_country;
    }

    public function pickup_at()
    {
        $pickupAt = new \DateTime('2000-01-01', new \DateTimeZone('Europe/London'));
        $pickupAt->add(new \DateInterval('PT1H'));
        return $pickupAt->format(JsonToJob::$STUART_DATE_FORMAT);
    }

    public function dropoff_at()
    {
        $dropoffAt = new \DateTime('2000-01-01', new \DateTimeZone('Europe/London'));
        $dropoffAt->add(new \DateInterval('PT1H'));
        return $dropoffAt->format(JsonToJob::$STUART_DATE_FORMAT);
    }


    public function drop_off_address()
    {
        return $this->drop_off_address_street
            . ', ' . $this->drop_off_address_postcode
            . ', ' . $this->drop_off_address_city
            . ', ' . $this->drop_off_address_country;
    }

    public function job_request_json()
    {
        return json_encode(
            array(
                'job' => array(
                    'transport_type' => 'bike',
                    'assignment_code' => $this->assignment_code,
                    'pickup_at' => $this->pickup_at(),
                    'dropoff_at' => $this->dropoff_at(),
                    'pickups' => array(
                        array(
                            'address' => $this->pickup_address(),
                            'comment' => $this->pickup_comment,
                            'contact' => array(
                                'firstname' => $this->pickup_contact_first_name,
                                'lastname' => $this->pickup_contact_last_name,
                                'phone' => $this->pickup_contact_phone,
                                'email' => $this->pickup_contact_email,
                                'company' => $this->pickup_contact_company
                            )
                        )
                    ),
                    'dropoffs' => array(
                        array(
                            'address' => $this->drop_off_address(),
                            'comment' => $this->drop_off_comment,
                            'contact' => array(
                                'firstname' => $this->drop_off_contact_first_name,
                                'lastname' => $this->drop_off_contact_last_name,
                                'phone' => $this->drop_off_contact_phone,
                                'email' => $this->drop_off_contact_email,
                                'company' => $this->drop_off_contact_company
                            ),
                            'package_type' => $this->drop_off_package_type,
                            'package_description' => $this->drop_off_package_description,
                            'client_reference' => $this->drop_off_client_reference
                        )
                    )
                )
            )
        );
    }

    public function job_creation_response_json()
    {
        return json_encode(
            array(
                'id' => $this->id,
                'status' => $this->status,
                'transport_type' => $this->transport_type,
                'assignment_code' => $this->assignment_code,
                'pickup_at' => $this->pickup_at(),
                'dropoff_at' => $this->dropoff_at(),
                'distance' => $this->distance,
                'duration' => $this->duration,
                'deliveries' => array(
                    0 => array(
                        'id' => $this->delivery_id,
                        'package_type' => $this->drop_off_package_type,
                        'package_description' => $this->drop_off_package_description,
                        'client_reference' => $this->drop_off_client_reference,
                        'tracking_url' => $this->delivery_tracking_url,
                        'status' => $this->delivery_status,
                        'pickup' => array(
                            'comment' => $this->pickup_comment,
                            'address' => array(
                                'street' => $this->pickup_address_street,
                                'postcode' => $this->pickup_address_postcode,
                                'city' => $this->pickup_address_city,
                                'country' => $this->pickup_address_country
                            ),
                            'contact' => array(
                                'firstname' => $this->pickup_contact_first_name,
                                'lastname' => $this->pickup_contact_last_name,
                                'phone' => $this->pickup_contact_phone,
                                'email' => $this->pickup_contact_email,
                                'company_name' => $this->pickup_contact_company
                            )
                        ),
                        'dropoff' => array(
                            'comment' => $this->drop_off_comment,
                            'address' => array(
                                'street' => $this->drop_off_address_street,
                                'postcode' => $this->drop_off_address_postcode,
                                'city' => $this->pickup_address_city,
                                'country' => $this->pickup_address_country
                            ),
                            'contact' => array(
                                'firstname' => $this->drop_off_contact_first_name,
                                'lastname' => $this->drop_off_contact_last_name,
                                'phone' => $this->drop_off_contact_phone,
                                'email' => $this->drop_off_contact_email,
                                'company_name' => $this->drop_off_contact_company
                            )
                        )
                    )
                )
            )
        );
    }

    public function job_pricing_response_json()
    {
        return json_encode(
            array(
                'amount' => 11.5,
                'currency' => "EUR"
            )
        );
    }

    public function job_eta_response_json()
    {
        return json_encode(
            array(
                'eta' => 672
            )
        );
    }

    public function address_validate_response_json()
    {
        return json_encode(
            array(
                'success' => true
            )
        );
    }

    public function scheduling_slots_response_json()
    {
        return json_encode(
            array(
                'date' => '2017-07-20T00:00:00.000+01:00',
                'zone' => array (
                    'id' => 2,
                    'region_id' => 189,
                    'name' => 'London',
                    'code' => 'london',
                    'timezone' => 'Europe/London',
                    'latitude' => 51.5286416,
                    'longitude' => -0.1015987,
                    'routes_to_avoid' => array (
                        'highways',
                        'ferries',
                    ),
                    'short_code' => 'lnd',
                    'ops_mail' => 'shaheen@stuart.com',
                    'locale' => 'en',
                ),
                'type' => 'pickup',
                'slots' => array (
                    array (
                        'start_time' => '2017-07-20T08:45:00.000+01:00',
                        'end_time' => '2017-07-20T09:00:00.000+01:00',
                    ),
                    array (
                        'start_time' => '2017-07-20T09:00:00.000+01:00',
                        'end_time' => '2017-07-20T09:15:00.000+01:00',
                    ),
                ),
            )
        );
    }
}
