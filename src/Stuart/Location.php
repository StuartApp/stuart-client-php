<?php

namespace Stuart;

abstract class Location
{
    private $address;
    private $comment;
    private $phone;
    private $email;
    private $firstName;
    private $lastName;
    private $company;

    public function setContactPhone($phone)
    {
        $this->phone = $phone;
        return $this;
    }

    public function setContactEmail($email)
    {
        $this->email = $email;
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

    public function getAddress()
    {
        return $this->address;
    }

    public function setAddress($address)
    {
        $this->address = $address;
        return $this;
    }

    public function getComment()
    {
        return $this->comment;
    }

    public function setComment($comment)
    {
        $this->comment = $comment;
        return $this;
    }

    public function getPhone()
    {
        return $this->phone;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function getFirstName()
    {
        return $this->firstName;
    }

    public function getLastName()
    {
        return $this->lastName;
    }

    public function getCompany()
    {
        return $this->company;
    }
}
