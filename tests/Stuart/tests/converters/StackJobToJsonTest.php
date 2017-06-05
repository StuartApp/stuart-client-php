<?php

namespace Stuart\Tests\Converters;

use \Stuart\Converters\StackedJobToJson;

class StackJobToJsonTest extends \PHPUnit_Framework_TestCase
{
    private $mock;

    public function setUp()
    {
        $this->mock = new Mock();
    }

    public function test_it_produces_expected_json()
    {
        self::assertEquals(
            StackedJobToJson::convert($this->mock->job()),
            $this->expected_json_body()
        );
    }

    private function expected_json_body()
    {
        return json_encode(
            array(
                'job' => array(
                    'pickup_at' => $this->mock->pickup_at()->format(\DateTime::ATOM),
                    'dropoff_at' => $this->mock->dropoff_at()->format(\DateTime::ATOM),
                    'pickups' => array(
                        array(
                            'address' => $this->mock->pickup_address(),
                            'comment' => $this->mock->pickup_comment,
                            'contact' => array(
                                'firstname' => $this->mock->pickup_contact_first_name,
                                'lastname' => $this->mock->pickup_contact_last_name,
                                'phone' => $this->mock->pickup_contact_phone,
                                'company' => $this->mock->pickup_contact_company
                            )
                        )
                    ),
                    'dropoffs' => array(
                        array(
                            'address' => $this->mock->drop_off_address(),
                            'comment' => $this->mock->drop_off_comment,
                            'contact' => array(
                                'firstname' => $this->mock->drop_off_contact_first_name,
                                'lastname' => $this->mock->drop_off_contact_last_name,
                                'phone' => $this->mock->drop_off_contact_phone,
                                'company' => $this->mock->drop_off_contact_company
                            ),
                            'package_type' => $this->mock->drop_off_package_type,
                            'package_description' => $this->mock->drop_off_package_description,
                            'client_reference' => $this->mock->drop_off_client_reference
                        )
                    )
                )
            )
        );
    }
}
