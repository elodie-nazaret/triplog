<?php

namespace AppBundle\DataFixtures\ORM;

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