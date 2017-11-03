<?php

namespace TrainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Trip
 *
 * @ORM\Table(name="trip")
 * @ORM\Entity(repositoryClass="TrainBundle\Repository\TripRepository")
 * @Gedmo\SoftDeleteable(fieldName="deleted_at", timeAware=false)
 */
class Trip
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
     * @var int
     *
     * @ORM\OneToOne(targetEntity="TrainBundle\Entity\OrderTrip", cascade={"persist"})
     * @ORM\JoinColumn(nullable=true)
     */
    private $order;

    /**
     * @ORM\ManyToOne(targetEntity="Station")
     * @ORM\JoinColumn(name="departure_station_id", referencedColumnName="id")
     */
    private $departureStationId;

    /**
     * @ORM\ManyToOne(targetEntity="Station")
     * @ORM\JoinColumn(name="arrival_station_id", referencedColumnName="id")
     */
    private $arrivalStationId;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="from_departure_date", type="datetime")
     */
    private $fromDepartureDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="to_departure_date", type="datetime")
     */
    private $toDepartureDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="return_date", type="datetime", nullable=true)
     */
    private $returnDate;

    /**
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_reserved", type="boolean")
     */
    private $isReserved = false;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_notified", type="boolean")
     */
    private $isNotified = false;

    /**
     * @var bool
     *
     * @ORM\Column(name="booking", type="boolean")
     */
    private $booking;

    /**
     * @var bool
     *
     * @ORM\Column(name="notification", type="boolean")
     */
    private $notification = false;

    /**
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $created_at;

    /**
     * @ORM\Column(name="updated_at", type="datetime")
     * @Gedmo\Timestampable(on="update")
     */
    private $updated_at;

    /**
     * @ORM\Column(name="deleted_at", type="datetime", nullable=true)
     */
    private $deleted_at;


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
     * Set departureStationId
     *
     * @param integer $departureStationId
     *
     * @return Trip
     */
    public function setDepartureStationId($departureStationId)
    {
        $this->departureStationId = $departureStationId;

        return $this;
    }

    /**
     * Get departureStationId
     *
     * @return int
     */
    public function getDepartureStationId()
    {
        return $this->departureStationId;
    }

    /**
     * Set arrivalStationId
     *
     * @param integer $arrivalStationId
     *
     * @return Trip
     */
    public function setArrivalStationId($arrivalStationId)
    {
        $this->arrivalStationId = $arrivalStationId;

        return $this;
    }

    /**
     * Get arrivalStationId
     *
     * @return int
     */
    public function getArrivalStationId()
    {
        return $this->arrivalStationId;
    }

    /**
     * Set returnDate
     *
     * @param \DateTime $returnDate
     *
     * @return Trip
     */
    public function setReturnDate($returnDate)
    {
        $this->returnDate = $returnDate;

        return $this;
    }

    /**
     * Get returnDate
     *
     * @return \DateTime
     */
    public function getReturnDate()
    {
        return $this->returnDate;
    }

    /**
     * Set uuid
     *
     * @param string $uuid
     *
     * @return Trip
     */
    public function setUuid($uuid)
    {
        $this->uuid = $uuid;

        return $this;
    }

    /**
     * Get uuid
     *
     * @return string
     */
    public function getUuid()
    {
        return $this->uuid;
    }

    /**
     * Set user
     *
     * @param \TrainBundle\Entity\User $user
     *
     * @return Trip
     */
    public function setUser(\TrainBundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \TrainBundle\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set isReserved
     *
     * @param boolean $isReserved
     *
     * @return Trip
     */
    public function setIsReserved($isReserved)
    {
        $this->isReserved = $isReserved;

        return $this;
    }

    /**
     * Get isReserved
     *
     * @return bool
     */
    public function getIsReserved()
    {
        return $this->isReserved;
    }

    /**
     * Set isNotified
     *
     * @param boolean $isNotified
     *
     * @return Trip
     */
    public function setIsNotified($isNotified)
    {
        $this->isNotified = $isNotified;

        return $this;
    }

    /**
     * Get isNotified
     *
     * @return bool
     */
    public function getIsNotified()
    {
        return $this->isNotified;
    }

    /**
     * Set booking
     *
     * @param boolean $booking
     *
     * @return Trip
     */
    public function setBooking($booking)
    {
        $this->booking = $booking;

        return $this;
    }

    /**
     * Get booking
     *
     * @return bool
     */
    public function getBooking()
    {
        return $this->booking;
    }

    /**
     * Set notification
     *
     * @param boolean $notification
     *
     * @return Trip
     */
    public function setNotification($notification)
    {
        $this->notification = $notification;

        return $this;
    }

    /**
     * Get notification
     *
     * @return bool
     */
    public function getNotification()
    {
        return $this->notification;
    }

    /**
     * Set fromDepartureDate
     *
     * @param \DateTime $fromDepartureDate
     *
     * @return Trip
     */
    public function setFromDepartureDate($fromDepartureDate)
    {
        $this->fromDepartureDate = $fromDepartureDate;

        return $this;
    }

    /**
     * Get fromDepartureDate
     *
     * @return \DateTime
     */
    public function getFromDepartureDate()
    {
        return $this->fromDepartureDate;
    }

    /**
     * Set toDepartureDate
     *
     * @param \DateTime $toDepartureDate
     *
     * @return Trip
     */
    public function setToDepartureDate($toDepartureDate)
    {
        $this->toDepartureDate = $toDepartureDate;

        return $this;
    }

    /**
     * Get toDepartureDate
     *
     * @return \DateTime
     */
    public function getToDepartureDate()
    {
        return $this->toDepartureDate;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return Trip
     */
    public function setCreatedAt($createdAt)
    {
        $this->created_at = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->created_at;
    }

    /**
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     *
     * @return Trip
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updated_at = $updatedAt;

        return $this;
    }

    /**
     * Get updatedAt
     *
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updated_at;
    }

    /**
     * Set deletedAt
     *
     * @param \DateTime $deletedAt
     *
     * @return Trip
     */
    public function setDeletedAt($deletedAt)
    {
        $this->deleted_at = $deletedAt;

        return $this;
    }

    /**
     * Get deletedAt
     *
     * @return \DateTime
     */
    public function getDeletedAt()
    {
        return $this->deleted_at;
    }

    /**
     * Set order
     *
     * @param \TrainBundle\Entity\OrderTrip $order
     *
     * @return Trip
     */
    public function setOrder(\TrainBundle\Entity\OrderTrip $order)
    {
        $this->order = $order;

        return $this;
    }

    /**
     * Get order
     *
     * @return \TrainBundle\Entity\OrderTrip
     */
    public function getOrder()
    {
        return $this->order;
    }
}
