<?php

namespace App\Controller;

use App\Entity\Bonus;
use App\Entity\Campaign;
use App\Form\BonusType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class BonusController
 * @package App\Controller
 * @Route ("/campaign")
 */
class BonusController extends AbstractController
{
    /**
     * @Route("/{id}/bonus/new", name="app.bonus.new", requirements={"id": "[0-9]+"})
     */
    public function new(Request $request, $id): Response
    {
        $bonus = new Bonus();
        $form = $this->createForm(BonusType::class, $bonus);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $campRep = $this->getDoctrine()->getRepository(Campaign::class);
            $campaign = $campRep->findOneBy(['id' => $id]);
            $campaign->setDate();
            $bonus->setCampaign($campaign);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($bonus);
            $entityManager->persist($campaign);
            $entityManager->flush();

            return $this->redirectToRoute('app.campaign', array('id'=>$id));
        }

        return $this->render('bonus/new.html.twig', [
            'bonus' => $bonus,
            'form' => $form->createView(),
            'id'=>$id,
        ]);
    }
    /**
     * @Route("/{id}/bonus/{id2}/edit", name="app.bonus.edit", requirements={"id": "[0-9]+", "id2": "[0-9]+"})
     */
    public function edit(Request $request, $id, $id2):Response
    {
        $bonusRep = $this->getDoctrine()->getRepository(Bonus::class);
        $bonus = $bonusRep->findOneBy(['id' => $id2]);
        $form = $this->createForm(BonusType::class, $bonus);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            return $this->redirectToRoute('app.campaign', array('id'=>$id));
        }

        return $this->render('bonus/edit.html.twig', [
            'bonus' => $bonus,
            'form' => $form->createView(),
            'id'=>$id,
        ]);
    }
    /**
     * @Route("/bonus/{id}/delete", name="app.bonus.delete", requirements={"id": "[0-9]+"},
     *     methods={"POST"})
    */
    public function delete(Request $request, Bonus $bonus): Response
    {
        if ($this->isCsrfTokenValid('delete'.$bonus->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($bonus);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app.campaign', array('id'=>$bonus->getCampaign()->getId()));
    }
}
