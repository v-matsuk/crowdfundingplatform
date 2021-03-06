<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Bonus;
use App\Entity\Campaign;
use App\Entity\Payment;
use App\Entity\User;
use App\Form\ProfileType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/profile")
 */

class ProfileController extends AbstractController
{
    /**
    * @Route("/{id}", name="app.profile", requirements={"id": "[0-9]+"})
    */
    public function index($id): Response
    {
        $uRep = $this->getDoctrine()->getRepository(User::class);
        $user = $uRep->findOneBy(['id' => $id]);
        $campRep = $this->getDoctrine()->getRepository(Campaign::class);
        $campaigns = $campRep->findBy(['user' => $id]);
        $paymentRep = $this->getDoctrine()->getRepository(Payment::class);
        $payments = $paymentRep->findBy(['user' => $id]);

        return $this->render('profile/profile.html.twig', [
            "user" => $user,
            "campaigns" => $campaigns,
            'payments' => $payments,
        ]);
    }
    /**
     * @Route("/{id}/edit", name="app.profile.edit", requirements={"id": "[0-9]+"})
     */
    public function edit(Request $request, User $user):Response
    {
        $form = $this->createForm(ProfileType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('app.profile', array('id'=>$user));
        }

        return $this->render('profile/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }
}
