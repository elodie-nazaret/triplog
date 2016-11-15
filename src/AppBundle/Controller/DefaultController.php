<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Trip;
use AppBundle\Form\TripType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        // replace this example code with whatever you need
        return $this->render('default/index.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.root_dir') . '/..') . DIRECTORY_SEPARATOR,
        ]);
    }

    /**
     * @Route("/trip/{id}", name="trip")
     */
    public function tripAction(Request $request, Trip $trip)
    {
        return $this->render(
            'trip/show.html.twig',
            ['trip' => $trip]
        );
    }

    /**
     * @Route("create/trip", name="create_trip")
     */
    public function createTripAction(Request $request)
    {
        $trip = new Trip();
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
}
