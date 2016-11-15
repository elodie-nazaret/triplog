<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Post;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;

use AppBundle\Entity\Trip;
use AppBundle\Entity\User;

/**
 * Class LoadDataDev
 */
class LoadDataDev implements FixtureInterface, ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    public function load(ObjectManager $manager)
    {
        $userManager = $this->container->get('fos_user.user_manager');

        /** @var User $user */
        $user = $userManager->createUser();
        $user->setUsername('test');
        $user->setPlainPassword('test');
        $user->setEmail('test@test.fr');
        $user->addRole('ROLE_USER');
        $user->setEnabled(true);
        $user->setFirstname('Toto');
        $user->setLastname('Titi');

        $userManager->updateUser($user);

        $trip = new Trip();
        $trip->setName('Voyage en Europe');
        $trip->setUser($user);

        $manager->persist($trip);

        $post1 = new Post();
        $post1->setTrip($trip);
        $post1->setAddress('7 rue Curie');
        $post1->setCity('Lyon');
        $post1->setContinent('Europe');
        $post1->setCountry('France');
        $post1->setDescription('Super beau !');
        $post1->setZipCode('69006');
        $post1->setLatitude(45.76958989);
        $post1->setLongitude(4.85930459);

        $manager->persist($post1);

        $post2 = new Post();
        $post2->setTrip($trip);
        $post2->setAddress('14 rue Gorge de Loup');
        $post2->setCity('Lyon');
        $post2->setContinent('Europe');
        $post2->setCountry('France');
        $post2->setDescription('Moins beau !');
        $post2->setZipCode('69009');
        $post2->setLatitude(45.7700392);
        $post2->setLongitude(4.80428410);

        $manager->persist($post2);

        $manager->flush();
    }

    /**
     * Sets the Container.
     *
     * @param ContainerInterface|null $container A ContainerInterface instance or null
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }
}