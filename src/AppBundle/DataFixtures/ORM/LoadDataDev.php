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
        $user->setUsername('john');
        $user->setPlainPassword('doe');
        $user->setEmail('john@doe.fr');
        $user->addRole('ROLE_USER');
        $user->setEnabled(true);
        $user->setFirstname('John');
        $user->setLastname('Doe');

        $userManager->updateUser($user);

        /** @var User $user2 */
        $user2 = $userManager->createUser();
        $user2->setUsername('foo');
        $user2->setPlainPassword('bar');
        $user2->setEmail('foo@bar.fr');
        $user2->addRole('ROLE_USER');
        $user2->setEnabled(true);
        $user2->setFirstname('Foo');
        $user2->setLastname('Bar');

        $userManager->updateUser($user2);

        /** @var User $user3 */
        $user3 = $userManager->createUser();
        $user3->setUsername('thierry');
        $user3->setPlainPassword('moulin');
        $user3->setEmail('thierry@moulin.fr');
        $user3->addRole('ROLE_USER');
        $user3->setEnabled(true);
        $user3->setFirstname('Thierry');
        $user3->setLastname('Moulin');

        $userManager->updateUser($user3);

        $trip = new Trip();
        $trip->setName('Voyage en France');
        $trip->setUser($user);

        $manager->persist($trip);

        $post1 = new Post();
        $post1->setTrip($trip);
        $post1->setAddress('5 Avenue Anatole France');
        $post1->setCity('Lyon');
        $post1->setContinent('Europe');
        $post1->setCountry('Paris');
        $post1->setDescription('La vue est magnifique du haut de la tour eiffel !');
        $post1->setZipCode('75007');
        $post1->setLatitude(48.858391);
        $post1->setLongitude(2.294426);

        $manager->persist($post1);

        $post2 = new Post();
        $post2->setTrip($trip);
        $post2->setAddress('Orly Airport');
        $post2->setCity('Orly');
        $post2->setContinent('Europe');
        $post2->setCountry('France');
        $post2->setDescription('Prêt à prendre l\'avion pour Lyon.');
        $post2->setZipCode('94390');
        $post2->setLatitude(48.726303);
        $post2->setLongitude(2.36515);

        $manager->persist($post2);

        $post3 = new Post();
        $post3->setTrip($trip);
        $post3->setAddress('Aéroport Lyon Saint-Exupéry');
        $post3->setCity('Colombier-Saugnieu');
        $post3->setContinent('Europe');
        $post3->setCountry('France');
        $post3->setDescription('Bien arrivé à Lyon ! Direction les traboules.');
        $post3->setZipCode('69125');
        $post3->setLatitude(45.731479);
        $post3->setLongitude(5.071339);

        $manager->persist($post3);

        $post4 = new Post();
        $post4->setTrip($trip);
        $post4->setAddress('21 Rue du Bœuf');
        $post4->setCity('Lyon');
        $post4->setContinent('Europe');
        $post4->setCountry('France');
        $post4->setDescription('Magnifique !');
        $post4->setZipCode('69005');
        $post4->setLatitude(45.762344);
        $post4->setLongitude(4.826818);

        $manager->persist($post4);

        $post5 = new Post();
        $post5->setTrip($trip);
        $post5->setAddress('17 Rue de la Loge');
        $post5->setCity('Marseille');
        $post5->setContinent('Europe');
        $post5->setCountry('France');
        $post5->setDescription('La plage !');
        $post5->setZipCode('13002');
        $post5->setLatitude(43.296371);
        $post5->setLongitude(5.369428);

        $manager->persist($post5);



        $trip2 = new Trip();
        $trip2->setName('Voyage en Chine');
        $trip2->setUser($user2);

        $manager->persist($trip2);

        $post6 = new Post();
        $post6->setTrip($trip2);
        $post6->setAddress('6 Zheng Yi Lu');
        $post6->setCity('Pékin');
        $post6->setContinent('Asie');
        $post6->setCountry('Chine');
        $post6->setDescription('La différence des cultures est énorme !');
        $post6->setZipCode('100006');
        $post6->setLatitude(39.903975);
        $post6->setLongitude(116.406691);

        $manager->persist($post6);



        $trip3 = new Trip();
        $trip3->setName('Voyage en Amérique');
        $trip3->setUser($user3);

        $manager->persist($trip3);

        $post7 = new Post();
        $post7->setTrip($trip3);
        $post7->setAddress('City Hall Park Path');
        $post7->setCity('New York');
        $post7->setContinent('Amérique du Nord');
        $post7->setCountry('Etats-Unis');
        $post7->setDescription('American dream !');
        $post7->setZipCode('NY 10007');
        $post7->setLatitude(40.712858);
        $post7->setLongitude(-74.005719);

        $manager->persist($post6);



        $trip4 = new Trip();
        $trip4->setName('Voyage Espagne Maroc');
        $trip4->setUser($user);

        $manager->persist($trip4);

        $post8 = new Post();
        $post8->setTrip($trip4);
        $post8->setAddress('17 Asquith Rd');
        $post8->setCity('Boksburg');
        $post8->setContinent('Europe');
        $post8->setCountry('Afrique du Sud');
        $post8->setDescription('Afrique du sud !');
        $post8->setZipCode('1459');
        $post8->setLatitude(-26.193712);
        $post8->setLongitude(28.245002);

        $manager->persist($post8);

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