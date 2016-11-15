<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Class Post
 *
 * @ORM\Entity(repositoryClass="AppBundle\Entity\TripRepository")
 * @ORM\Table(name="post")
 */
class Post
{
    use ORMBehaviors\Timestampable\Timestampable;

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(type="boolean")
     */
    private $interestPoint = false;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Trip", inversedBy="posts")
     * @ORM\JoinColumn(name="trip_id", referencedColumnName="id")
     */
    private $trip;

    /**
     * @var float
     *
     * @ORM\Column(type="float", name="latitude"))
     */
    private $latitude;

    /**
     * @var float
     *
     * @ORM\Column(type="float", name="longitude"))
     */
    private $longitude;

    /**
     * @var string
     *
     * @ORM\Column(type="string", name="continent"))
     */
    private $continent;

    /**
     * @var string
     *
     * @ORM\Column(type="string", name="country"))
     */
    private $country;

    /**
     * @var string
     *
     * @ORM\Column(type="string", name="city"))
     */
    private $city;

    /**
     * @var string
     *
     * @ORM\Column(type="string", name="zip_code"))
     */
    private $zipCode;

    /**
     * @var string
     *
     * @ORM\Column(type="string", name="address"))
     */
    private $address;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Picture", mappedBy="post", cascade={"persist", "remove"})
     */
    private $pictures;

    /**
     * Post constructor.
     */
    public function __construct()
    {
        $this->pictures = new ArrayCollection();
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param mixed $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return mixed
     */
    public function getInterestPoint()
    {
        return $this->interestPoint;
    }

    /**
     * @param mixed $interestPoint
     */
    public function setInterestPoint($interestPoint)
    {
        $this->interestPoint = $interestPoint;
    }

    /**
     * @return mixed
     */
    public function getTrip()
    {
        return $this->trip;
    }

    /**
     * @param mixed $trip
     */
    public function setTrip($trip)
    {
        $this->trip = $trip;
    }

    /**
     * @return float
     */
    public function getLatitude()
    {
        return $this->latitude;
    }

    /**
     * @param float $latitude
     */
    public function setLatitude($latitude)
    {
        $this->latitude = $latitude;
    }

    /**
     * @return float
     */
    public function getLongitude()
    {
        return $this->longitude;
    }

    /**
     * @param float $longitude
     */
    public function setLongitude($longitude)
    {
        $this->longitude = $longitude;
    }

    /**
     * @return string
     */
    public function getContinent()
    {
        return $this->continent;
    }

    /**
     * @param string $continent
     */
    public function setContinent($continent)
    {
        $this->continent = $continent;
    }

    /**
     * @return string
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * @param string $country
     */
    public function setCountry($country)
    {
        $this->country = $country;
    }

    /**
     * @return mixed
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * @param mixed $city
     */
    public function setCity($city)
    {
        $this->city = $city;
    }

    /**
     * @return mixed
     */
    public function getZipCode()
    {
        return $this->zipCode;
    }

    /**
     * @param mixed $zipCode
     */
    public function setZipCode($zipCode)
    {
        $this->zipCode = $zipCode;
    }

    /**
     * @return mixed
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @param mixed $address
     */
    public function setAddress($address)
    {
        $this->address = $address;
    }

    /**
     * @return mixed
     */
    public function getPictures()
    {
        return $this->pictures;
    }

    /**
     * @param mixed $pictures
     */
    public function setPictures($pictures)
    {
        $this->pictures = $pictures;
    }

    /**
     * @param mixed $picture
     */
    public function addPictures($picture)
    {
        $this->pictures->add($picture);
    }

    /**
     * @param mixed $picture
     */
    public function removePicture($picture)
    {
        $this->pictures->removeElement($picture);
    }
}