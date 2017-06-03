<?php

namespace Stuart;

abstract class Location
{
    private $address;
    private $comment;
    private $phone;
    private $firstName;
    private $lastName;
    private $company;

    public function setAddress($address)
    {
        $this->address = $address;
        return $this;
    }

    public function setComment($comment)
    {
        $this->comment = $comment;
        return $this;
    }

    public function setContactPhone($phone)
    {
        $this->phone = $phone;
        return $this;
    }

    public function setContactFirstName($firstName)
    {
        $this->firstName = $firstName;
        return $this;
    }

    public function setContactLastName($lastName)
    {
        $this->lastName = $lastName;
        return $this;
    }

    public function setContactCompany($company)
    {
        $this->company = $company;
        return $this;
    }

    public function toArray()
    {
        return array(
            'address' => $this->address,
            'comment' => $this->comment,
            'contact' => array(
                'firstname' => $this->firstName,
                'lastname' => $this->lastName,
                'phone' => $this->phone,
                'company' => $this->company
            )
        );
    }
}