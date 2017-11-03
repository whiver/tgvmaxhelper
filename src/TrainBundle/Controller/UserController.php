<?php

namespace TrainBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\DependencyInjection\ContainerInterface;
use TrainBundle\Entity\UserCard;
use TrainBundle\Form\UserCardType;

/**
 * @Route("/profile/card")
 */
class UserController extends Controller
{

    /**
     * @Route("/", name="card")
     */
    public function indexAction()
    {
        $repository = $this->getDoctrine()->getRepository(UserCard::class);
        $card = $repository->findOneByUser($this->getUser());

        return $this->render('TrainBundle:User:UserCard\index.html.twig', array(
            'card' => $card
        ));

    }

    /**
     * @Route("/add", name="add_card")
     */
    public function newAction(Request $request)
    {
        $card = new UserCard();
        $form = $this->createForm(UserCardType::class, $card);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $card = $form->getData();
            $card->setUser($this->getUser());

            try{
                $em = $this->getDoctrine()->getManager();
                $em->persist($card);
                $em->flush();
            }
            catch(\Exception $e){
                $this->addFlash('error', 'La carte n\'a pas été créée avec comme erreur: '.$e->getMessage());
                return $this->redirectToRoute('fos_user_profile_show');
            }

            if ($card->getId()) {
                $this->addFlash('success', 'La carte a été ajoutée!');
                return $this->redirectToRoute('fos_user_profile_show');
            }
            $this->addFlash('error', 'La carte n\'a pas été ajoutée!');
            return $this->redirectToRoute('fos_user_profile_show');
        }

        return $this->render('TrainBundle:User:UserCard\new.html.twig', array(
            'form' => $form->createView(),
        ));

    }

    /**
     * @Route("/edit/{id}", name="edit_card")
     */
    public function editAction(UserCard $card, Request $request)
    {
        if (!$card) {
            throw $this->createNotFoundException('La carte n\'existe pas');
        }

        if(!$card->getUser()->getId() == $this->getUser()->getId()) {
            $this->addFlash('notice', 'Vous n\'êtes pas autorisé à voir ce contenu!');
            return $this->redirectToRoute('fos_user_profile_show');
        }

        $form = $this->createForm(UserCardType::class, $card);

        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);

            if ($form->isValid()) {
                try{
                    $em = $this->getDoctrine()->getManager();
                    $em->flush();
                }
                catch(\Exception $e){
                    $this->addFlash('error', 'La carte n\'a pas été modifiée avec comme erreur: '.$e->getMessage());
                    return $this->redirectToRoute('fos_user_profile_show');
                }

                if ($card->getId()) {
                    $this->addFlash('success', 'La carte a été modifiée!');
                    return $this->redirectToRoute('fos_user_profile_show');
                }

                $this->addFlash('error', 'La carte n\'a pas été modifiée!');
                return $this->redirectToRoute('fos_user_profile_show');
            }
        }

        return $this->render('TrainBundle:User:UserCard\edit.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/delete/{id}", name="delete_card")
     */
    public function removeAction(UserCard $card)
    {
        if (!$card) {
            throw $this->createNotFoundException('La carte n\'existe pas');
        }

        if(!$card->getUser()->getId() == $this->getUser()->getId()) {
            $this->addFlash('notice', 'Vous n\'êtes pas autorisé à voir ce contenu!');
            return $this->redirectToRoute('fos_user_profile_show');
        }

        try{
            $em = $this->getDoctrine()->getManager();
            $em->remove($card);
            $em->flush();
        }
        catch(\Exception $e){
            $this->addFlash('error', 'La carte n\'a pas été supprimée avec comme erreur: '.$e->getMessage());
            return $this->redirectToRoute('fos_user_profile_show');
        }

        $this->addFlash('success', 'La carte a été supprimée!');
        return $this->redirectToRoute('fos_user_profile_show');
    }

}