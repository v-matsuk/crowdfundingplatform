<?php

namespace App\Controller;

use App\Entity\Campaign;
use App\Entity\Rating;
use App\Form\VoteType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/campaign")
 */
class RatingController extends AbstractController
{
    /**
     * @Route("/{id}/vote", name="app.vote", requirements={"id": "[0-9]+"})
     */
    public function new(Request $request, $id): Response
    {
        $rating = new Rating();
        $form = $this->createForm(VoteType::class, $rating);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->get('security.token_storage')->getToken()->getUser();
            $rating->setUser($user);
            $campRep = $this->getDoctrine()->getRepository(Campaign::class);
            $campaign = $campRep->findOneBy(['id' => $id]);
            $rating->setCampaign($campaign);
            $overallRating = $campaign->getRating() * $campaign->getNumberOfRatings()+$rating->getRating();
            $campaign->setNumberOfRatings($campaign->getNumberOfRatings()+1);
            $campaign->setRating($overallRating/$campaign->getNumberOfRatings());
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($rating);
            $entityManager->persist($campaign);
            $entityManager->flush();

            return $this->redirectToRoute('app.campaign', array('id'=>$id));
        }
        return $this->render('rating/new.html.twig', [
            'rating' => $rating,
            'form' => $form->createView(),
            'id' => $id,
        ]);
    }
}
