<?php

namespace TrainBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use TrainBundle\Entity\Trip;


class HomeController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction()
    {
        $repository = $this->getDoctrine()->getRepository(Trip::class);
        $trips = $repository->findByUser($this->getUser());

        return $this->render('TrainBundle::home.html.twig', array(
            'trips' => $trips
        ));
    }
}