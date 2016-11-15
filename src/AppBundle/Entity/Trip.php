<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Class Trip
 *
 * @ORM\Entity(repositoryClass="AppBundle\Entity\TripRepository")
 * @ORM\Table(name="trip")
 */
class Trip
{
    use ORMBehaviors\Timestampable\Timestampable;

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

	/**
     * @ORM\Column(type="string")
     */
	private $name;

	/**
	 * @ORM\OneToMany(targetEntity="AppBundle\Entity\Post", mappedBy="trip", cascade={"persist", "remove", "merge"})
	 * @ORM\JoinColumn(name="post_id", referencedColumnName="id")
	*/
	private $posts;

	/**
     * @ORM\Column(type="boolean", length=255, nullable=false)
     */
	private $status;

    /**
     * Trip constructor.
     */
    public function __construct()
    {
        $this->posts = new ArrayCollection();
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
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getPosts()
    {
        return $this->posts;
    }

    /**
     * @param mixed $posts
     */
    public function setPosts($posts)
    {
        $this->posts = $posts;
    }

    /**
     * @param mixed $post
     */
    public function addPost($post)
    {
        $this->posts->add($post);
    }

    /**
     * @param mixed $post
     */
    public function removePost($post)
    {
        $this->posts->removeElement($post);
    }

    /**
     * @return mixed
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param mixed $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }
}