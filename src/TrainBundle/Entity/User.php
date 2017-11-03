<?php

namespace TrainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\User as BaseUser;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * User
 *
 * @ORM\Table(name="user")
 * @ORM\Entity(repositoryClass="TrainBundle\Repository\UserRepository")
 */
class User extends BaseUser
{
    const GENDER_MALE = "M.";
    const GENDER_FEMALE = "Mme";

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="gender", columnDefinition="enum('M.', 'Mme')")
     */
    protected $gender;

    /**
     * @var string
     *
     * @ORM\Column(name="name", nullable=false)
     * @Assert\NotBlank(groups={"Registration", "Profile"})
     */
    protected $name;

    /**
     * @var string
     *
     * @ORM\Column(name="surname", nullable=false)
     * @Assert\NotBlank(groups={"Registration", "Profile"})
     */
    protected $surname;

    /**
     * @var date
     *
     * @ORM\Column(name="birthdate", nullable=false, type="date")
     * @Assert\Date(groups={"Registration", "Profile"})
     */
    protected $birthdate;


    /**
     * Set gender
     *
     * @param string $gender
     *
     * @return User
     */
    public function setGender($gender)
    {
        $this->gender = $gender;

        return $this;
    }

    /**
     * Get gender
     *
     * @return string
     */
    public function getGender()
    {
        return $this->gender;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return User
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
     * Set surname
     *
     * @param string $surname
     *
     * @return User
     */
    public function setSurname($surname)
    {
        $this->surname = $surname;

        return $this;
    }

    /**
     * Get surname
     *
     * @return string
     */
    public function getSurname()
    {
        return $this->surname;
    }

    /**
     * Set birthdate
     *
     * @param date $birthdate
     *
     * @return User
     */
    public function setBirthdate($birthdate)
    {
        $this->birthdate = $birthdate;

        return $this;
    }

    /**
     * Get birthdate
     *
     * @return date
     */
    public function getBirthdate()
    {
        return $this->birthdate;
    }

    /**
     * @return array
     */
    public function getPossibleGenderValue()
    {
        return [
            self::GENDER_MALE,
            self::GENDER_FEMALE
        ];
    }

    /**
     * @return int
     */
    public function getAge()
    {
        $from = new \DateTime($this->getBirthdate()->format('Y-m-d H:i:s'));
        $to   = new \DateTime('today');
        return $from->diff($to)->y;
    }



    /**
     * Add userCard
     *
     * @param \TrainBundle\Entity\UserCard $userCard
     *
     * @return User
     */
    public function addUserCard(\TrainBundle\Entity\UserCard $userCard)
    {
        $this->UserCard[] = $userCard;

        return $this;
    }

    /**
     * Remove userCard
     *
     * @param \TrainBundle\Entity\UserCard $userCard
     */
    public function removeUserCard(\TrainBundle\Entity\UserCard $userCard)
    {
        $this->UserCard->removeElement($userCard);
    }

    /**
     * Get userCard
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getUserCard()
    {
        return $this->UserCard;
    }

    /**
     * Set userCard
     *
     * @param \TrainBundle\Entity\UserCard $userCard
     *
     * @return User
     */
    public function setUserCard(\TrainBundle\Entity\UserCard $userCard = null)
    {
        $this->UserCard = $userCard;

        return $this;
    }
}
