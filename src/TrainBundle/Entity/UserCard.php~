<?php

namespace TrainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * UserCard
 *
 * @ORM\Table(name="user_card")
 * @ORM\Entity(repositoryClass="TrainBundle\Repository\UserCardRepository")
 * @UniqueEntity("discountCardNumber")
 */
class UserCard
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
     * @ORM\OneToOne(targetEntity="User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="DiscountCard")
     * @ORM\JoinColumn(name="discount_card_id", referencedColumnName="id")
     */
    private $discountCard;

    /**
     * @var string
     *
     * @ORM\Column(name="discount_card_number", type="string", length=255, nullable=true, unique=true)
     */
    private $discountCardNumber;

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
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set discountCardNumber
     *
     * @param string $discountCardNumber
     *
     * @return UserCard
     */
    public function setDiscountCardNumber($discountCardNumber)
    {
        $this->discountCardNumber = $discountCardNumber;

        return $this;
    }

    /**
     * Get discountCardNumber
     *
     * @return string
     */
    public function getDiscountCardNumber()
    {
        return $this->discountCardNumber;
    }

    /**
     * Set user
     *
     * @param \TrainBundle\Entity\User $user
     *
     * @return UserCard
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
     * Set discountCard
     *
     * @param \TrainBundle\Entity\DiscountCard $discountCard
     *
     * @return UserCard
     */
    public function setDiscountCard(\TrainBundle\Entity\DiscountCard $discountCard = null)
    {
        $this->discountCard = $discountCard;

        return $this;
    }

    /**
     * Get discountCard
     *
     * @return \TrainBundle\Entity\DiscountCard
     */
    public function getDiscountCard()
    {
        return $this->discountCard;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return UserCard
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
     * @return UserCard
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
}
