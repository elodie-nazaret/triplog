<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\User as BaseUser;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;
use Symfony\Component\Validator\Constraints as Assert;
use Algolia\AlgoliaSearchBundle\Mapping\Annotation as Algolia;

/**
 * Class User
 *
 * @ORM\Entity(repositoryClass="AppBundle\Entity\UserRepository")
 * @ORM\Table(name="fos_user")
 * @Algolia\Index(autoIndex=false)
 */
class User extends BaseUser
{
    use ORMBehaviors\Timestampable\Timestampable;

    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Algolia\Attribute()
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(type="string", name="first_name", nullable=true))
     * @Algolia\Attribute()
     */
    private $firstName;

    /**
     * @var string
     *
     * @ORM\Column(type="string", name="last_name", nullable=true))
     * @Algolia\Attribute()
     */
    private $lastName;

    /**
     * @var Trip[]
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Trip", mappedBy="user", cascade={"persist"})
     **/
    private $trips;

    /**
     * User constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->trips = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return Trip[]
     */
    public function getTrips()
    {
        return $this->trips;
    }

    /**
     * @param Trip[] $trips
     */
    public function setTrips($trips)
    {
        $this->trips = $trips;
    }

    /**
     * @param Trip $project
     */
    public function addTrip(Trip $project)
    {
        $this->trips->add($project);
    }

    /**
     * @param Trip $project
     */
    public function removeTrip(Trip $project)
    {
        $this->trips->removeElement($project);
    }

    /**
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * @param string $firstName
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;
    }

    /**
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * @param string $lastName
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        if ($this->getFirstName() && $this->getLastName()) {
            return (string) $this->getFirstName().' '.$this->getLastName();
        } else {
            return (string) $this->getUsername();
        }
    }
}