<?php

namespace Stuart\tests;

use Stuart\converters\JsonToStackedJob;
use Stuart\Converters\StackedJobToJson;
use Stuart\JobStacked;

class JobStackedTest extends \PHPUnit_Framework_TestCase
{


    private $pickup_address_street = '12 rue de rivoli';
    private $pickup_address_postcode = '75004';
    private $pickup_address_country = 'france';
    private $pickup_address_city = 'paris';

    private function pickup_address()
    {
        return $this->pickup_address_street
            . ', ' . $this->pickup_address_postcode
            . ', ' . $this->pickup_address_city
            . ', ' . $this->pickup_address_country;
    }

    private $pickup_comment = 'comment';
    private $pickup_contact_company = 'company';
    private $pickup_contact_first_name = 'firstname';
    private $pickup_contact_last_name = 'lastname';
    private $pickup_contact_phone = '837746';

    private function pickup_at()
    {
        $pickupAt = new \DateTime('now', new \DateTimeZone('Europe/London'));
        $pickupAt->add(new \DateInterval('PT1H'));
        return $pickupAt;
    }

    private $drop_off_address_street = '148 rue de charenton';
    private $drop_off_address_postcode = '75012';
    private $drop_off_address_country = 'france';
    private $drop_off_address_city = 'paris';

    private function drop_off_address()
    {
        return $this->drop_off_address_street
            . ', ' . $this->drop_off_address_postcode
            . ', ' . $this->drop_off_address_city
            . ', ' . $this->drop_off_address_country;
    }

    private $drop_off_comment = 'comment';
    private $drop_off_contact_company = 'company';
    private $drop_off_contact_first_name = 'firstname';
    private $drop_off_contact_last_name = 'lastname';
    private $drop_off_contact_phone = '837746';
    private $drop_off_client_reference = 'reference';
    private $drop_off_package_description = 'decription';
    private $drop_off_package_type = 'small';

    private function dropoff_at()
    {
        $pickupAt = new \DateTime('now', new \DateTimeZone('Europe/London'));
        $pickupAt->add(new \DateInterval('PT2H'));
        return $pickupAt;
    }

    private function job()
    {
        $job = new JobStacked();

        $job->addPickup($this->pickup_address())
            ->setPickupAt($this->pickup_at())
            ->setComment($this->pickup_comment)
            ->setContactCompany($this->pickup_contact_company)
            ->setContactFirstName($this->pickup_contact_first_name)
            ->setContactLastName($this->pickup_contact_last_name)
            ->setContactPhone($this->pickup_contact_phone);

        $job->addDropOff($this->drop_off_address())
            ->setDropOffAt($this->dropoff_at())
            ->setComment($this->drop_off_comment)
            ->setContactCompany($this->drop_off_contact_company)
            ->setContactFirstName($this->drop_off_contact_first_name)
            ->setContactLastName($this->drop_off_contact_last_name)
            ->setContactPhone($this->drop_off_contact_phone)
            ->setClientReference($this->drop_off_client_reference)
            ->setPackageDescription($this->drop_off_package_description)
            ->setPackageType($this->drop_off_package_type);

        return $job;
    }

    public function test_it_produces_expected_pickups_and_drop_offs()
    {
        self::assertEquals(JsonToStackedJob::convert($this->expected_json_body_resp()), $this->job());
    }

    public function test_it_produces_expected_json()
    {
        self::assertEquals($this->expected_json_body(), StackedJobToJson::convert($this->job()));
    }

    private function expected_json_body()
    {
        return json_encode(
            array(
                'job' => array(
                    'pickup_at' => $this->pickup_at()->format(\DateTime::ATOM),
                    'dropoff_at' => $this->dropoff_at()->format(\DateTime::ATOM),
                    'pickups' => array(
                        array(
                            'address' => $this->pickup_address(),
                            'comment' => $this->pickup_comment,
                            'contact' => array(
                                'firstname' => $this->pickup_contact_first_name,
                                'lastname' => $this->pickup_contact_last_name,
                                'phone' => $this->pickup_contact_phone,
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

    private function expected_json_body_resp()
    {
        return json_encode(
            array(
                'pickup_at' => $this->pickup_at()->format(\DateTime::ATOM),
                'dropoff_at' => $this->dropoff_at()->format(\DateTime::ATOM),
                'deliveries' => array(
                    0 => array(
                        'package_type' => $this->drop_off_package_type,
                        'package_description' => $this->drop_off_package_description,
                        'client_reference' => $this->drop_off_client_reference,
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
                                'company' => $this->pickup_contact_company
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
                                'company' => $this->drop_off_contact_company
                            )
                        )
                    )
                )
            )
        );
    }
}
