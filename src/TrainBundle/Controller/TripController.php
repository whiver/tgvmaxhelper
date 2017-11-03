<?php

namespace TrainBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use TrainBundle\Entity\Trip;
use TrainBundle\Form\TripType;

/**
 * @Route("/trip")
 */
class TripController extends Controller
{
    /**
     * @Route("/", name="trip")
     */
    public function indexAction()
    {
        $repository = $this->getDoctrine()->getRepository(Trip::class);
        $trips = $repository->findByUser($this->getUser());

        return $this->render('TrainBundle::home.html.twig', array(
            'trips' => $trips
        ));
    }

    /**
     * @Route("/show/{id}", name="show_trip")
     *
     * @param Trip $trip
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function showAction(Trip $trip)
    {
        if(!$trip) {
            throw $this->createNotFoundException('Le voyage n\'existe pas');
        }

        if(!$trip->getUser()->getId() == $this->getUser()->getId()) {
            $this->addFlash('notice', 'Vous n\'êtes pas autorisé à voir ce contenu!');
            return $this->redirectToRoute('trip');
        }

        return $this->render('TrainBundle:Trip:show.html.twig', array(
            'trip' => $trip
        ));
    }

    /**
     * @Route("/cancel/{id}", name="cancel_trip")
     *
     * @param Trip $trip
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function cancelAction(Trip $trip) {
        $trainline = $this->get('train.trainline_manager');

        if(!$trip) {
            throw $this->createNotFoundException('Le voyage n\'existe pas');
        }

        if(!$trip->getUser()->getId() == $this->getUser()->getId()) {
            $this->addFlash('notice', 'Vous n\'êtes pas autorisé à voir ce contenu!');
            return $this->redirectToRoute('trip');
        }

        $cancel = $trainline->cancelBooking($trip->getId());

        if($cancel){
            $this->addFlash('success', 'Le voyage a été annulé!');
            return $this->redirectToRoute('trip');
        }
        $this->addFlash('error', 'Le voyage n\'a pas été annulé!');
        return $this->redirectToRoute('trip');
    }

    /**
     * @Route("/new", name="new_trip")
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function newAction(Request $request)
    {
        $trip = new Trip();
        $form = $this->createForm(TripType::class, $trip);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $trip = $form->getData();
            $trip->setUser($this->getUser());

            try{
                $em = $this->getDoctrine()->getManager();
                $em->persist($trip);
                $em->flush();
            }
            catch(\Exception $e){
                $this->addFlash('error', 'Le voyage n\'a pas été créé avec comme erreur: '.$e->getMessage());
                return $this->redirectToRoute('trip');
            }

            if ($trip->getId()) {
                $this->addFlash('success', 'Le voyage a été ajouté!');
                return $this->redirectToRoute('trip');
            }
            $this->addFlash('error', 'Le voyage n\'a pas été ajouté!');
            return $this->redirectToRoute('trip');
        }

        return $this->render('TrainBundle:Trip:new.html.twig', array(
            'form' => $form->createView(),
        ));

    }

    /**
     * @Route("/edit/{id}", name="edit_trip")
     *
     * @param Trip $trip
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function editAction(Trip $trip, Request $request)
    {
        if (!$trip) {
            throw $this->createNotFoundException('Le voyage n\'existe pas');
        }

        if(!$trip->getUser()->getId() == $this->getUser()->getId()) {
            $this->addFlash('notice', 'Vous n\'êtes pas autorisé à voir ce contenu!');
            return $this->redirectToRoute('trip');
        }

        $form = $this->createForm(TripType::class, $trip);

        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);

            if ($form->isValid()) {
                try{
                    $em = $this->getDoctrine()->getManager();
                    $em->flush();
                }
                catch(\Exception $e){
                    $this->addFlash('error', 'Le voyage n\'a pas été modifié avec comme erreur: '.$e->getMessage());
                    return $this->redirectToRoute('trip');
                }

                if ($trip->getId()) {
                    $this->addFlash('success', 'Le voyage a été modifié!');
                    return $this->redirectToRoute('trip');
                }

                $this->addFlash('error', 'Le voyage n\'a pas été modifié!');
                return $this->redirectToRoute('trip');
            }
        }

        return $this->render('TrainBundle:Trip:edit.html.twig', array(
            'trip' => $trip,
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/delete/{id}", name="delete_trip")
     *
     * @param Trip $trip
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function removeAction(Trip $trip)
    {
        if (!$trip) {
            throw $this->createNotFoundException('Le voyage n\'existe pas');
        }

        if(!$trip->getUser()->getId() == $this->getUser()->getId()) {
            $this->addFlash('notice', 'Vous n\'êtes pas autorisé à voir ce contenu!');
            return $this->redirectToRoute('trip');
        }

        try{
            $em = $this->getDoctrine()->getManager();
            $em->remove($trip);
            $em->flush();
        }
        catch(\Exception $e){
            $this->addFlash('error', 'Le voyage n\'a pas été supprimé avec comme erreur: '.$e->getMessage());
            return $this->redirectToRoute('trip');
        }

        $this->addFlash('success', 'Le voyage a été supprimé!');
        return $this->redirectToRoute('trip');
    }

}