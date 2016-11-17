<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use AppBundle\Entity\Picture;
use AppBundle\Entity\Post;
use AppBundle\Entity\Trip;
use AppBundle\Form\PostType;
use AppBundle\Form\TripEditType;
use AppBundle\Form\TripType;
use Doctrine\Common\Collections\ArrayCollection;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use GuzzleHttp\Client;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        $facets = ['posts.country', 'posts.continent'];

        $countries  = $this->getDoctrine()->getRepository(Post::class)->getAllCountries();
        $continents = $this->getDoctrine()->getRepository(Post::class)->getAllContinents();

        return $this->render('default/index.html.twig', [
            'algolia_app_id'         => $this->getParameter('algolia.application_id'),
            'algolia_search_api_key' => $this->getParameter('algolia_search_api_key'),
            'algolia_index_name'     => $this->getParameter('algolia_default_index'),
            'facets'                 => $facets,
            'countries'              => $countries,
            'continents'             => $continents,
        ]);
    }

    /**
     * @Route("/trip/{id}", name="trip")
     */
    public function tripAction(Request $request, Trip $trip)
    {
        return $this->render(
            'trip/show.html.twig',
            [
                'trip' => $trip,
            ]
        );
    }

    /**
     * @Route("trips/create", name="create_trip")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     */
    public function createTripAction(Request $request)
    {
        $trip = new Trip();
        $trip->setUser($this->getUser());

        $form = $this->createForm(TripType::class, $trip);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($trip);
            $em->flush();

            return $this->redirectToRoute('trip', ['id' => $trip->getId()]);
        }

        return $this->render(
            'trip/form.html.twig',
            ['form' => $form->createView()]
        );
    }

    /**
     * @Route("/user/{id}", name="user")
     */
    public function userAction(Request $request, User $user)
    {
        return $this->render(
            'user/show.html.twig',
            ['user' => $user]
        );
    }

    /**
     * @Route("trips/edit/{id}", name="edit_trip")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     */
    public function editTripAction(Request $request, Trip $trip)
    {
        $form = $this->createForm(TripEditType::class, $trip);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($trip);
            $em->flush();

            return $this->redirectToRoute('trip', ['id' => $trip->getId()]);
        }

        return $this->render(
            'trip/form.html.twig',
            ['form' => $form->createView()]
        );
    }

    /**
     * @Route("/post/{id}", name="post_content")
     */
    public function postAction(Request $request, Post $post)
    {
        return $this->render(
            'post/show.html.twig',
            [
                'post' => $post,
            ]
        );
    }

    /**
     * @Route("{id}/posts/create", name="create_post")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     */
    public function createPostAction(Request $request, Trip $trip)
    {
        if ($request->isMethod(Request::METHOD_GET)) {
            $this->deleteTmpFiles();
        }

        $post = new Post();
        $post->setTrip($trip);

        $form = $this->createForm(PostType::class, $post);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $post = $form->getData();
            $geoData = $this->getCoordinates($request, $post->getLatitude(), $post->getLongitude());

            $post->setContinent($geoData['continent']);
            $post->setCountry($geoData['country']);
            $post->setCity($geoData['city']);
            $post->setZipCode($geoData['zip_code']);
            $post->setAddress($geoData['address']);

            $adapter    = $this->get('knp_gaufrette.filesystem_map')->get('real_gallery');
            $adapterTmp = $this->get('knp_gaufrette.filesystem_map')->get('gallery');

            foreach ($post->getPictures() as $picture) {
                $content = $adapterTmp->read($picture->getPath());

                if ($adapter->has($trip->getId().DIRECTORY_SEPARATOR.$picture->getPath())) {
                    $fileName  = substr($picture->getPath(), 0, strrpos($picture->getPath(), '.'));
                    $extension = substr($picture->getPath(), strrpos($picture->getPath(), '.') + 1);

                    $picture->setPath($fileName.'_'.$picture->getId().'.'.$extension);
                    $this->getDoctrine()->getManager()->persist($picture);
                }
                $adapter->write($trip->getId().DIRECTORY_SEPARATOR.$picture->getPath(), $content);
            }

            $em = $this->getDoctrine()->getManager();
            $em->persist($post);
            $em->flush();

            $this->get('algolia.indexer')->getManualIndexer($em)->reIndex('AppBundle:Trip');

            return $this->redirectToRoute('trip', ['id' => $trip->getId()]);
        }

        return $this->render(
            'post/form.html.twig',
            ['form' => $form->createView()]
        );
    }

    /**
     * @Route("posts/edit/{id}", name="edit_post")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     */
    public function editPostAction(Request $request, Post $post)
    {
        if ($request->isMethod(Request::METHOD_GET)) {
            $this->deleteTmpFiles();
        }

        $form = $this->createForm(PostType::class, $post);

        $originalPictures = new ArrayCollection();

        foreach ($post->getPictures() as $picture) {
            $originalPictures->add($picture);
        }

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $adapter    = $this->get('knp_gaufrette.filesystem_map')->get('real_gallery');
            $adapterTmp = $this->get('knp_gaufrette.filesystem_map')->get('gallery');

            /** @var Picture $originalPicture */
            foreach ($originalPictures as $originalPicture) {
                if (false === $form->getData()->getPictures()->contains($originalPicture)) {
                    $adapter->delete($post->getTrip()->getId().'/'.$originalPicture->getPath());
                    $this->getDoctrine()->getManager()->remove($originalPicture);
                }
            }

            foreach ($post->getPictures() as $picture) {
                if (false === $originalPictures->contains($picture)) {
                    $content = $adapterTmp->read($picture->getPath());

                    if ($adapter->has($post->getTrip()->getId().'/'.$picture->getPath())) {
                        $fileName  = substr($picture->getPath(), 0, strrpos($picture->getPath(), '.'));
                        $extension = substr($picture->getPath(), strrpos($picture->getPath(), '.') + 1);

                        $picture->setPath($fileName.'_'.$picture->getId().'.'.$extension);
                        $this->getDoctrine()->getManager()->persist($picture);
                    }

                    $adapter->write($post->getTrip()->getId().DIRECTORY_SEPARATOR.$picture->getPath(), $content);
                }
            }

            $em = $this->getDoctrine()->getManager();
            $em->persist($post);
            $em->flush();

            $this->get('algolia.indexer')->getManualIndexer($em)->reIndex('AppBundle:Trip');

            return $this->redirectToRoute('trip', ['id' => $post->getTrip()->getId()]);
        }

        return $this->render(
            'post/form.html.twig',
            ['form' => $form->createView(), 'post' => $post]
        );
    }

    /**
     * Delete all files that are in tmp directory
     */
    private function deleteTmpFiles()
    {
        $adapterTmp = $this->get('knp_gaufrette.filesystem_map')->get('gallery');
        $filesTmp   = $adapterTmp->listKeys();

        foreach ($filesTmp['keys'] as $fileTmp) {
            $adapterTmp->delete($fileTmp);
        }
    }

    /**
     * @param string $lat
     * @param string $lng
     *
     * @return array|null
     */
    public function getCoordinates(Request $request,$lat, $lng)
    {
        $client = new Client();
        $response = $client->request(
            'GET',
            'https://maps.googleapis.com/maps/api/geocode/json',
            [
                'query' => [
                    'sensor' => false,
                    'key'    => 'AIzaSyCQzpA6VBvo70B0M71IYZY1payfYYOd2yc',
                    'latlng' => $lat.','.$lng,
                ],
            ]
        );

        $data = json_decode($response->getBody(), true);

        if ($data['status'] == "ZERO_RESULTS" || $data['status'] == "OVER_QUERY_LIMIT") {
            return null;
        }

        $data = $data['results'][0];

        foreach ($data['address_components'] as $element) {
            $data[implode(' ', $element['types'])] = $element['long_name'];
        }

        $address = '';
        $address .= (array_key_exists('street_number', $data)) ? $data['street_number'].' ' : '';
        $address .= (array_key_exists('route', $data)) ? $data['route'] : '';

        $shortCountryName = $this->getShortName($data['address_components']);
        $countryContinent = json_decode(file_get_contents(getcwd().DIRECTORY_SEPARATOR.'bundles'.DIRECTORY_SEPARATOR.'app'.DIRECTORY_SEPARATOR.'country.json'), true);

        return [
            'continent' => $this->getContinentName($countryContinent, $shortCountryName),
            'country'   => (array_key_exists('country political', $data) ? $data['country political'] : ''),
            'city'      => (array_key_exists('locality political', $data) ? $data['locality political'] : ''),
            'zip_code'  => (array_key_exists('postal_code', $data) ? $data['postal_code'] : ''),
            'address'   => $address,
        ];
    }

    private function getShortName($addrComponents)
    {
        for ($i = 0; $i < count($addrComponents); $i++) {
            if ($addrComponents[$i]['types'][0] == "country") {
                return $addrComponents[$i]['short_name'];
            }

            if (count($addrComponents[$i]['types']) == 2) {
                if ($addrComponents[$i]['types'][0] == "political") {
                    return $addrComponents[$i]['short_name'];
                }
            }
        }

        return false;
    }

    private function getContinentName($countryContinent, $shortCountryName)
    {
        foreach ($countryContinent['countries']['country'] as $country) {
            if ($country['countryCode'] == $shortCountryName) {
                return $country['continentName'];
            }
        }

        return false;
    }
}
