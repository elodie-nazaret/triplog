<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;
use Doctrine\Common\Collections\ArrayCollection;
use Algolia\AlgoliaSearchBundle\Mapping\Annotation as Algolia;

/**
 * Class Trip
 *
 * @ORM\Entity(repositoryClass="AppBundle\Entity\TripRepository")
 * @ORM\Table(name="trip")
 * @Algolia\Index(algoliaName="triplog", perEnvironment=false)
 */
class Trip
{
    use ORMBehaviors\Timestampable\Timestampable;

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Algolia\Attribute(algoliaName="trip_id")
     */
    private $id;

	/**
     * @ORM\Column(type="string")
     * @Algolia\Attribute()
     */
	private $name;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User", inversedBy="trips")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     * @Algolia\Attribute()
     */
    private $user;

    /**
	 * @ORM\OneToMany(targetEntity="AppBundle\Entity\Post", mappedBy="trip", cascade={"persist", "remove", "merge"})
	 * @ORM\JoinColumn(name="post_id", referencedColumnName="id")
     * @ORM\OrderBy({"createdAt" = "DESC"})
     * @Algolia\Attribute()
	*/
	private $posts;

	/**
     * @ORM\Column(type="boolean", nullable=false)
     * @Algolia\Attribute()
     */
	private $status = false;

    /**
     * Trip constructor.
     */
    public function __construct()
    {
        $this->posts = new ArrayCollection();
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
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return ArrayCollection
     */
    public function getPosts()
    {
        return $this->posts;
    }

    /**
     * @param Post $posts
     */
    public function setPosts($posts)
    {
        $this->posts = $posts;
    }

    /**
     * @param Post $post
     */
    public function addPost($post)
    {
        $this->posts->add($post);
    }

    /**
     * @param Post $post
     */
    public function removePost($post)
    {
        $this->posts->removeElement($post);
    }

    /**
     * @return bool
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param bool $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param User $user
     */
    public function setUser($user)
    {
        $user->addTrip($this);
        $this->user = $user;
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

    /**
     * @Algolia\Attribute(algoliaName="nb_posts")
     *
     * @return int
     */
    public function getNbPosts()
    {
        return $this->getPosts()->count();
    }

    /**
     * @Algolia\Attribute(algoliaName="first_picture")
     *
     * @return bool|array
     */
    public function getFirstPicture()
    {
        /** @var Post $post */
        foreach ($this->posts as $post) {
            if (!$post->getPictures()->isEmpty()) {
                return [
                    'path'        => $post->getPictures()->first()->getPath(),
                    'picture_url' => 'media/cache/resolve/s/'.$post->getPictures()->first()->getWebPath(),
                ];
            }
        }

        return false;
    }
}