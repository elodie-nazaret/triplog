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
        $posts = $trip->getPosts();

        return $this->render(
            'trip/show.html.twig',
            [
                'trip' => $trip,
                'posts' => $posts,
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

            //$this->get('algolia.indexer')->getManualIndexer($em)->reIndex('AppBundle:Post');

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

            //$this->get('algolia.indexer')->getManualIndexer($em)->reIndex('AppBundle:Post');

            return $this->redirectToRoute('trip', ['id' => $trip->getId()]);
        }

        return $this->render(
            'trip/form.html.twig',
            ['form' => $form->createView()]
        );
    }

    /**
     * @Route("/post/{id}", name="post")
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
            $adapter    = $this->get('knp_gaufrette.filesystem_map')->get('real_gallery');
            $adapterTmp = $this->get('knp_gaufrette.filesystem_map')->get('gallery');

            foreach ($post->getPictures() as $picture) {
                $content = $adapterTmp->read($picture->getPath());

                if ($adapter->has($post->getId().'/'.$picture->getPath())) {
                    $fileName  = substr($picture->getPath(), 0, strrpos($picture->getPath(), '.'));
                    $extension = substr($picture->getPath(), strrpos($picture->getPath(), '.') + 1);

                    $picture->setPath($fileName.'_'.$picture->getId().'.'.$extension);
                    $this->getDoctrine()->getManager()->persist($picture);
                }

                $adapter->write($picture->getPath(), $content);
                $adapter->rename($picture->getPath(), $post->getId().'/'.$picture->getPath());
            }

            $em = $this->getDoctrine()->getManager();
            $em->persist($post);
            $em->flush();

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
                    $adapter->delete($post->getId().'/'.$originalPicture->getPath());
                    $this->getDoctrine()->getManager()->remove($originalPicture);
                }
            }

            foreach ($post->getPictures() as $picture) {
                if (false === $originalPictures->contains($picture)) {
                    $content = $adapterTmp->read($picture->getPath());

                    if ($adapter->has($post->getId().'/'.$picture->getPath())) {
                        $fileName  = substr($picture->getPath(), 0, strrpos($picture->getPath(), '.'));
                        $extension = substr($picture->getPath(), strrpos($picture->getPath(), '.') + 1);

                        $picture->setPath($fileName.'_'.$picture->getId().'.'.$extension);
                        $this->getDoctrine()->getManager()->persist($picture);
                    }

                    $adapter->write($picture->getPath(), $content);
                    $adapter->rename($picture->getPath(), $post->getId().'/'.$picture->getPath());

                    //generate m filter for PDF
                    $this->get('liip_imagine.controller')->filterAction(new Request(), $picture->getWebPath(), 'm');
                }
            }

            $em = $this->getDoctrine()->getManager();
            $em->persist($post);
            $em->flush();

            return $this->redirectToRoute('trip', ['id' => $post->getTrip()->getId()]);
        }

        return $this->render(
            'post/form.html.twig',
            ['form' => $form->createView()]
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
}
