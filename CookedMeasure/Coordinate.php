<?php

namespace A5sys\QowisioCloudApiBundle\CookedMeasure;

/**
 * Represents a GPS coordinate, with latitude and longitude
 */
class Coordinate implements \JsonSerializable
{
    protected $latitude;

    protected $longitude;

    protected $timestampCollected;

    /**
     * Constructor
     * @param mixed string|float $latitude
     * @param mixed string|float $longitude
     * @param string             $timestampCollected
     */
    public function __construct($latitude, $longitude, $timestampCollected)
    {
        $this
            ->setLatitude($latitude)
            ->setLongitude($longitude)
            ->setTimestampCollected($timestampCollected)
        ;
    }

    /**
     *
     * @return float
     */
    function getLatitude()
    {
        return $this->latitude;
    }

    /**
     *
     * @return float
     */
    function getLongitude()
    {
        return $this->longitude;
    }

    /**
     *
     * @return integer
     */
    function getTimestampCollected()
    {
        return $this->timestampCollected;
    }

    /**
     * converts the timestampCollected to a \DateTime object
     * The date is computed from the given timestamp, but is converted in the given timezone (system timezone if not provided).
     * Use getUtcDateCollected to get an UTC date
     * @param $timezone the timezone to use. System timezone if null or not provided
     * @return \DateTime
     */
    function getDateCollected($timezone = null)
    {
        $date = new \DateTime('@'.$this->timestampCollected);
        $date->setTimezone(new \DateTimeZone($timezone ? $timezone : date_default_timezone_get()));

        return $date;
    }

    /**
     * converts the timestampCollected to a \DateTime object
     * Warning, the date is given UTC
     * @return \DateTime
     */
    function getUtcDateCollected()
    {
        return new \DateTime('@'.$this->timestampCollected);
    }

    /**
     *
     * @param mixed string|float $latitude
     * @return \QowisioCloudApiBundle\CookedMeasure\Coordinate
     */
    function setLatitude($latitude)
    {
        $this->latitude = floatval($latitude);

        return $this;
    }

    /**
     *
     * @param mixed string|float $longitude
     * @return \QowisioCloudApiBundle\CookedMeasure\Coordinate
     */
    function setLongitude($longitude)
    {
        $this->longitude = floatval($longitude);

        return $this;
    }

    /**
     *
     * @param string $timestampCollected
     * @return \QowisioCloudApiBundle\CookedMeasure\Coordinate
     */
    function setTimestampCollected($timestampCollected)
    {
        $this->timestampCollected = intval($timestampCollected);

        return $this;
    }

    /**
     * Implementation of JsonSerializable needed for json_encode
     * @return array
     */
    public function jsonSerialize()
    {
        return [
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'timestampCollected' => $this->timestampCollected,
            'dateCollected' => $this->getDateCollected(),
            'dateUtcCollected' => $this->getUtcDateCollected(),
        ];
    }
}
