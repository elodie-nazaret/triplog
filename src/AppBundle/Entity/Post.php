<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;
use Doctrine\Common\Collections\ArrayCollection;
use Algolia\AlgoliaSearchBundle\Mapping\Annotation as Algolia;

/**
 * Class Post
 *
 * @ORM\Entity(repositoryClass="AppBundle\Entity\TripRepository")
 * @ORM\Table(name="post")
 * @Algolia\Index(algoliaName="triplog", perEnvironment=false)
 */
class Post
{
    use ORMBehaviors\Timestampable\Timestampable;

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Algolia\Attribute(algoliaName="post_id")
     */
    private $id;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Algolia\Attribute()
     */
    private $description;

    /**
     * @ORM\Column(type="boolean")
     * @Algolia\Attribute()
     */
    private $interestPoint = false;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Trip", inversedBy="posts")
     * @ORM\JoinColumn(name="trip_id", referencedColumnName="id")
     * @Algolia\Attribute()
     */
    private $trip;

    /**
     * @var float
     *
     * @ORM\Column(type="float", name="latitude"))
     * @Algolia\Attribute()
     */
    private $latitude;

    /**
     * @var float
     *
     * @ORM\Column(type="float", name="longitude"))
     * @Algolia\Attribute()
     */
    private $longitude;

    /**
     * @var string
     *
     * @ORM\Column(type="string", name="continent"))
     * @Algolia\Attribute()
     */
    private $continent;

    /**
     * @var string
     *
     * @ORM\Column(type="string", name="country"))
     * @Algolia\Attribute()
     */
    private $country;

    /**
     * @var string
     *
     * @ORM\Column(type="string", name="city"))
     * @Algolia\Attribute()
     */
    private $city;

    /**
     * @var string
     *
     * @ORM\Column(type="string", name="zip_code"))
     * @Algolia\Attribute()
     */
    private $zipCode;

    /**
     * @var string
     *
     * @ORM\Column(type="string", name="address"))
     * @Algolia\Attribute()
     */
    private $address;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Picture", mappedBy="post", cascade={"persist", "remove"})
     * @Algolia\Attribute()
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
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return bool
     */
    public function getInterestPoint()
    {
        return $this->interestPoint;
    }

    /**
     * @param bool $interestPoint
     */
    public function setInterestPoint($interestPoint)
    {
        $this->interestPoint = $interestPoint;
    }

    /**
     * @return Trip
     */
    public function getTrip()
    {
        return $this->trip;
    }

    /**
     * @param Trip $trip
     */
    public function setTrip($trip)
    {
        $trip->addPost($this);
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
     * @return int
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * @param int $city
     */
    public function setCity($city)
    {
        $this->city = $city;
    }

    /**
     * @return int
     */
    public function getZipCode()
    {
        return $this->zipCode;
    }

    /**
     * @param int $zipCode
     */
    public function setZipCode($zipCode)
    {
        $this->zipCode = $zipCode;
    }

    /**
     * @return string
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @param string $address
     */
    public function setAddress($address)
    {
        $this->address = $address;
    }

    /**
     * @return ArrayCollection
     */
    public function getPictures()
    {
        return $this->pictures;
    }

    /**
     * @param ArrayCollection $pictures
     */
    public function setPictures($pictures)
    {
        $this->pictures = $pictures;
    }

    /**
     * @param Picture $picture
     */
    public function addPicture($picture)
    {
        $picture->setPost($this);
        $this->pictures->add($picture);
    }

    /**
     * @param Picture $picture
     */
    public function removePicture($picture)
    {
        $this->pictures->removeElement($picture);
    }

    /**
     * @Algolia\Attribute(algoliaName="_geoloc")
     *
     * @return array
     */
    public function getGeoloc()
    {
        return array(
            'lat' => $this->getLatitude(),
            'lng' => $this->getLongitude(),
        );
    }

    /**
     * @Algolia\Attribute(algoliaName="creation_date")
     *
     * @return \DateTime
     */
    public function getCreationDate()
    {
        return $this->getCreatedAt();
    }
}