<?php

namespace TrainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Station
 *
 * @ORM\Table(name="station")
 * @ORM\Entity(repositoryClass="TrainBundle\Repository\StationRepository")
 */
class Station
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="text")
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="slug", type="string", length=255)
     */
    private $slug;

    /**
     * @var int
     *
     * @ORM\Column(name="uic", type="integer", nullable=true)
     */
    private $uic;

    /**
     * @var int
     *
     * @ORM\Column(name="uic8_sncf", type="integer", nullable=true)
     */
    private $uic8Sncf;

    /**
     * @var string
     *
     * @ORM\Column(name="latitude", type="decimal", precision=10, scale=8, nullable=true)
     */
    private $latitude;

    /**
     * @var string
     *
     * @ORM\Column(name="longitude", type="decimal", precision=11, scale=8, nullable=true)
     */
    private $longitude;

    /**
     * @var int
     *
     * @ORM\Column(name="parent_station_id", type="integer", nullable=true)
     */
    private $parentStationId;

    /**
     * @var string
     *
     * @ORM\Column(name="country", type="string", length=2)
     */
    private $country;

    /**
     * @var string
     *
     * @ORM\Column(name="time_zone", type="string", length=255)
     */
    private $timeZone;

    /**
     * @var string
     *
     * @ORM\Column(name="sncf_id", type="string", length=5, nullable=true)
     */
    private $sncfId;

    /**
     * @var string
     *
     * @ORM\Column(name="sncf_tvs_id", type="string", length=3, nullable=true)
     */
    private $sncfTvsId;

    /**
     * @var int
     *
     * @ORM\Column(name="db_id", type="integer", nullable=true)
     */
    private $dbId;

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Station
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set slug
     *
     * @param string $slug
     *
     * @return Station
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * Get slug
     *
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Set uic
     *
     * @param integer $uic
     *
     * @return Station
     */
    public function setUic($uic)
    {
        $this->uic = $uic;

        return $this;
    }

    /**
     * Get uic
     *
     * @return int
     */
    public function getUic()
    {
        return $this->uic;
    }

    /**
     * Set uic8Sncf
     *
     * @param integer $uic8Sncf
     *
     * @return Station
     */
    public function setUic8Sncf($uic8Sncf)
    {
        $this->uic8Sncf = $uic8Sncf;

        return $this;
    }

    /**
     * Get uic8Sncf
     *
     * @return int
     */
    public function getUic8Sncf()
    {
        return $this->uic8Sncf;
    }

    /**
     * Set latitude
     *
     * @param string $latitude
     *
     * @return Station
     */
    public function setLatitude($latitude)
    {
        $this->latitude = $latitude;

        return $this;
    }

    /**
     * Get latitude
     *
     * @return string
     */
    public function getLatitude()
    {
        return $this->latitude;
    }

    /**
     * Set longitude
     *
     * @param string $longitude
     *
     * @return Station
     */
    public function setLongitude($longitude)
    {
        $this->longitude = $longitude;

        return $this;
    }

    /**
     * Get longitude
     *
     * @return string
     */
    public function getLongitude()
    {
        return $this->longitude;
    }

    /**
     * Set parentStationId
     *
     * @param integer $parentStationId
     *
     * @return Station
     */
    public function setParentStationId($parentStationId)
    {
        $this->parentStationId = $parentStationId;

        return $this;
    }

    /**
     * Get parentStationId
     *
     * @return int
     */
    public function getParentStationId()
    {
        return $this->parentStationId;
    }

    /**
     * Set country
     *
     * @param string $country
     *
     * @return Station
     */
    public function setCountry($country)
    {
        $this->country = $country;

        return $this;
    }

    /**
     * Get country
     *
     * @return string
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * Set timeZone
     *
     * @param string $timeZone
     *
     * @return Station
     */
    public function setTimeZone($timeZone)
    {
        $this->timeZone = $timeZone;

        return $this;
    }

    /**
     * Get timeZone
     *
     * @return string
     */
    public function getTimeZone()
    {
        return $this->timeZone;
    }

    /**
     * Set sncfId
     *
     * @param string $sncfId
     *
     * @return Station
     */
    public function setSncfId($sncfId)
    {
        $this->sncfId = $sncfId;

        return $this;
    }

    /**
     * Get sncfId
     *
     * @return string
     */
    public function getSncfId()
    {
        return $this->sncfId;
    }

    /**
     * Set sncfTvsId
     *
     * @param string $sncfTvsId
     *
     * @return Station
     */
    public function setSncfTvsId($sncfTvsId)
    {
        $this->sncfTvsId = $sncfTvsId;

        return $this;
    }

    /**
     * Get sncfTvsId
     *
     * @return string
     */
    public function getSncfTvsId()
    {
        return $this->sncfTvsId;
    }

    /**
     * Set dbId
     *
     * @param integer $dbId
     *
     * @return Station
     */
    public function setDbId($dbId)
    {
        $this->dbId = $dbId;

        return $this;
    }

    /**
     * Get dbId
     *
     * @return int
     */
    public function getDbId()
    {
        return $this->dbId;
    }
}

