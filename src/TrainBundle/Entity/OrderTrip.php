<?php

namespace TrainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * OrderTrip
 *
 * @ORM\Table(name="order_trip")
 * @ORM\Entity(repositoryClass="TrainBundle\Repository\OrderTripRepository")
 * @Gedmo\SoftDeleteable(fieldName="deleted_at", timeAware=false)
 */
class OrderTrip
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
     * @ORM\ManyToOne(targetEntity="TrainBundle\Entity\User", cascade={"persist"})
     */
    private $user;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="departure_date", type="datetime", nullable=true)
     */
    private $departureDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="arrival_date", type="datetime", nullable=true)
     */
    private $arrivalDate;

    /**
     * @var string
     *
     * @ORM\Column(name="folder_id", type="string", nullable=true, unique=true)
     * @ORM\JoinColumn(nullable=false)
     */
    private $folderId;

    /**
     * @var string
     *
     * @ORM\Column(name="direction", type="string", length=255, nullable=true)
     */
    private $direction;

    /**
     * @var int
     *
     * @ORM\Column(name="search_id", type="integer", nullable=true, unique=true)
     */
    private $searchId;

    /**
     * @var int
     *
     * @ORM\Column(name="order_id", type="string", nullable=true, unique=true)
     */
    private $orderId;

    /**
     * @var int
     *
     * @ORM\Column(name="pnr_id", type="integer", nullable=true, unique=true)
     */
    private $pnrId;

    /**
     * @var int
     *
     * @ORM\Column(name="pnrs_revision", type="integer", nullable=true, unique=true)
     */
    private $pnrsRevision;

    /**
     * @var int
     *
     * @ORM\Column(name="payment_id", type="integer", nullable=true, unique=true)
     */
    private $paymentId;

    /**
     * @var bool
     *
     * @ORM\Column(name="error", type="boolean")
     */
    private $error;

    /**
     * @var string
     *
     * @ORM\Column(name="passenger_uuid", type="string", nullable=true)
     */
    private $passengerUuid;

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
     * Set folderId
     *
     * @param integer $folderId
     *
     * @return OrderTrip
     */
    public function setFolderId($folderId)
    {
        $this->folderId = $folderId;

        return $this;
    }

    /**
     * Get folderId
     *
     * @return int
     */
    public function getFolderId()
    {
        return $this->folderId;
    }

    /**
     * Set direction
     *
     * @param string $direction
     *
     * @return OrderTrip
     */
    public function setDirection($direction)
    {
        $this->direction = $direction;

        return $this;
    }

    /**
     * Get direction
     *
     * @return string
     */
    public function getDirection()
    {
        return $this->direction;
    }

    /**
     * Set searchId
     *
     * @param integer $searchId
     *
     * @return OrderTrip
     */
    public function setSearchId($searchId)
    {
        $this->searchId = $searchId;

        return $this;
    }

    /**
     * Get searchId
     *
     * @return int
     */
    public function getSearchId()
    {
        return $this->searchId;
    }

    /**
     * Set orderId
     *
     * @param integer $orderId
     *
     * @return OrderTrip
     */
    public function setOrderId($orderId)
    {
        $this->orderId = $orderId;

        return $this;
    }

    /**
     * Get orderId
     *
     * @return int
     */
    public function getOrderId()
    {
        return $this->orderId;
    }

    /**
     * Set pnrId
     *
     * @param integer $pnrId
     *
     * @return OrderTrip
     */
    public function setPnrId($pnrId)
    {
        $this->pnrId = $pnrId;

        return $this;
    }

    /**
     * Get pnrId
     *
     * @return int
     */
    public function getPnrId()
    {
        return $this->pnrId;
    }

    /**
     * Set paymentId
     *
     * @param integer $paymentId
     *
     * @return OrderTrip
     */
    public function setPaymentId($paymentId)
    {
        $this->paymentId = $paymentId;

        return $this;
    }

    /**
     * Get paymentId
     *
     * @return int
     */
    public function getPaymentId()
    {
        return $this->paymentId;
    }

    /**
     * Set user
     *
     * @param \TrainBundle\Entity\User $user
     *
     * @return OrderTrip
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
     * Set error
     *
     * @param boolean $error
     *
     * @return OrderTrip
     */
    public function setError($error)
    {
        $this->error = $error;

        return $this;
    }

    /**
     * Get error
     *
     * @return boolean
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * Set pnrsRevision
     *
     * @param integer $pnrsRevision
     *
     * @return OrderTrip
     */
    public function setPnrsRevision($pnrsRevision)
    {
        $this->pnrsRevision = $pnrsRevision;

        return $this;
    }

    /**
     * Get pnrsRevision
     *
     * @return integer
     */
    public function getPnrsRevision()
    {
        return $this->pnrsRevision;
    }

    /**
     * Set passengerUuid
     *
     * @param string $passengerUuid
     *
     * @return OrderTrip
     */
    public function setPassengerUuid($passengerUuid)
    {
        $this->passengerUuid = $passengerUuid;

        return $this;
    }

    /**
     * Get passengerUuid
     *
     * @return string
     */
    public function getPassengerUuid()
    {
        return $this->passengerUuid;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return OrderTrip
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
     * @return OrderTrip
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
     * @return OrderTrip
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
     * Set departureDate
     *
     * @param \DateTime $departureDate
     *
     * @return OrderTrip
     */
    public function setDepartureDate($departureDate)
    {
        $this->departureDate = $departureDate;

        return $this;
    }

    /**
     * Get departureDate
     *
     * @return \DateTime
     */
    public function getDepartureDate()
    {
        return $this->departureDate;
    }

    /**
     * Set arrivalDate
     *
     * @param \DateTime $arrivalDate
     *
     * @return OrderTrip
     */
    public function setArrivalDate($arrivalDate)
    {
        $this->arrivalDate = $arrivalDate;

        return $this;
    }

    /**
     * Get arrivalDate
     *
     * @return \DateTime
     */
    public function getArrivalDate()
    {
        return $this->arrivalDate;
    }
}
