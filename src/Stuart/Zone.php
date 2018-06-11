<?php

namespace Stuart;

class Zone
{
    private $id;
    private $regionId;
    private $name;
    private $code;
    private $timezone;
    private $latitude;
    private $longitude;
    private $routesToAvoid = [];
    private $shortCode;
    private $ops_mail;
    private $locale;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     *
     * @return Zone
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getRegionId()
    {
        return $this->regionId;
    }

    /**
     * @param mixed $regionId
     *
     * @return Zone
     */
    public function setRegionId($regionId)
    {
        $this->regionId = $regionId;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     *
     * @return Zone
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @param mixed $code
     *
     * @return Zone
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getTimezone()
    {
        return $this->timezone;
    }

    /**
     * @param mixed $timezone
     *
     * @return Zone
     */
    public function setTimezone($timezone)
    {
        $this->timezone = $timezone;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getLatitude()
    {
        return $this->latitude;
    }

    /**
     * @param mixed $latitude
     *
     * @return Zone
     */
    public function setLatitude($latitude)
    {
        $this->latitude = $latitude;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getLongitude()
    {
        return $this->longitude;
    }

    /**
     * @param mixed $longitude
     *
     * @return Zone
     */
    public function setLongitude($longitude)
    {
        $this->longitude = $longitude;

        return $this;
    }

    /**
     * @return array
     */
    public function getRoutesToAvoid(): array
    {
        return $this->routesToAvoid;
    }

    /**
     * @param array $routesToAvoid
     *
     * @return Zone
     */
    public function setRoutesToAvoid(array $routesToAvoid): Zone
    {
        $this->routesToAvoid = $routesToAvoid;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getShortCode()
    {
        return $this->shortCode;
    }

    /**
     * @param mixed $shortCode
     *
     * @return Zone
     */
    public function setShortCode($shortCode)
    {
        $this->shortCode = $shortCode;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getOpsMail()
    {
        return $this->ops_mail;
    }

    /**
     * @param mixed $ops_mail
     *
     * @return Zone
     */
    public function setOpsMail($ops_mail)
    {
        $this->ops_mail = $ops_mail;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * @param mixed $locale
     *
     * @return Zone
     */
    public function setLocale($locale)
    {
        $this->locale = $locale;

        return $this;
    }
}
