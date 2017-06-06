<?php

namespace Stuart\Tests\Converters;

use \Stuart\Converters\JsonToJob;

class JsonToJobTest extends \PHPUnit_Framework_TestCase
{
    private $mock;

    public function setUp()
    {
        $this->mock = new Mock();
    }

    public function test_it_produces_expected_pickups_and_drop_offs()
    {
        self::assertEquals(
            JsonToJob::convert($this->expected_json_body_resp()),
            $this->mock->job()
        );
    }

    private function expected_json_body_resp()
    {
        return json_encode(
            array(
                'pickup_at' => $this->mock->pickup_at()->format(\DateTime::ATOM),
                'dropoff_at' => $this->mock->dropoff_at()->format(\DateTime::ATOM),
                'deliveries' => array(
                    0 => array(
                        'package_type' => $this->mock->drop_off_package_type,
                        'package_description' => $this->mock->drop_off_package_description,
                        'client_reference' => $this->mock->drop_off_client_reference,
                        'pickup' => array(
                            'comment' => $this->mock->pickup_comment,
                            'address' => array(
                                'street' => $this->mock->pickup_address_street,
                                'postcode' => $this->mock->pickup_address_postcode,
                                'city' => $this->mock->pickup_address_city,
                                'country' => $this->mock->pickup_address_country
                            ),
                            'contact' => array(
                                'firstname' => $this->mock->pickup_contact_first_name,
                                'lastname' => $this->mock->pickup_contact_last_name,
                                'phone' => $this->mock->pickup_contact_phone,
                                'company' => $this->mock->pickup_contact_company
                            )
                        ),
                        'dropoff' => array(
                            'comment' => $this->mock->drop_off_comment,
                            'address' => array(
                                'street' => $this->mock->drop_off_address_street,
                                'postcode' => $this->mock->drop_off_address_postcode,
                                'city' => $this->mock->pickup_address_city,
                                'country' => $this->mock->pickup_address_country
                            ),
                            'contact' => array(
                                'firstname' => $this->mock->drop_off_contact_first_name,
                                'lastname' => $this->mock->drop_off_contact_last_name,
                                'phone' => $this->mock->drop_off_contact_phone,
                                'company' => $this->mock->drop_off_contact_company
                            )
                        )
                    )
                )
            )
        );
    }
}
