<?php

namespace Stuart\Tests;

use \Stuart\Job;

class Mock
{
    public $id = '1234567';
    public $status = 'new';

    public $pickup_address_street = '12 rue de rivoli';
    public $pickup_address_postcode = '75004';
    public $pickup_address_country = 'france';
    public $pickup_address_city = 'paris';

    public function pickup_address()
    {
        return $this->pickup_address_street
            . ', ' . $this->pickup_address_postcode
            . ', ' . $this->pickup_address_city
            . ', ' . $this->pickup_address_country;
    }

    public $pickup_comment = 'comment';
    public $pickup_contact_company = 'company';
    public $pickup_contact_first_name = 'firstname';
    public $pickup_contact_last_name = 'lastname';
    public $pickup_contact_phone = '837746';

    public function pickup_at()
    {
        $pickupAt = new \DateTime('now', new \DateTimeZone('Europe/London'));
        $pickupAt->add(new \DateInterval('PT1H'));
        return $pickupAt;
    }

    public $drop_off_address_street = '148 rue de charenton';
    public $drop_off_address_postcode = '75012';
    public $drop_off_address_country = 'france';
    public $drop_off_address_city = 'paris';

    public function drop_off_address()
    {
        return $this->drop_off_address_street
            . ', ' . $this->drop_off_address_postcode
            . ', ' . $this->drop_off_address_city
            . ', ' . $this->drop_off_address_country;
    }

    public $drop_off_comment = 'comment';
    public $drop_off_contact_company = 'company';
    public $drop_off_contact_first_name = 'firstname';
    public $drop_off_contact_last_name = 'lastname';
    public $drop_off_contact_phone = '837746';
    public $drop_off_client_reference = 'reference';
    public $drop_off_package_description = 'decription';
    public $drop_off_package_type = 'small';

    public $drop_off_tracking_url = 'https://track-sandbox.stuart.com/tracking/delivery/1234/1234567890';

    public function dropoff_at()
    {
        $pickupAt = new \DateTime('now', new \DateTimeZone('Europe/London'));
        $pickupAt->add(new \DateInterval('PT2H'));
        return $pickupAt;
    }

    public function job()
    {
        $job = new Job();

        $job->setId($this->id);
        $job->setStatus($this->status);

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
            ->setPackageType($this->drop_off_package_type)
            ->setTrackingUrl($this->drop_off_tracking_url);

        return $job;
    }
}
