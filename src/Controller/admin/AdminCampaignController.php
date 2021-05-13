<?php

namespace App\Controller\admin;

use App\Entity\Campaign;
use App\Form\CampaignType;
use App\Repository\CampaignRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/campaign')]
class AdminCampaignController extends AbstractController
{
    #[Route('/', name: 'app.admin.campaign', methods: ['GET'])]
    public function index(CampaignRepository $campaignRepository): Response
    {
        return $this->render('admin/admin_campaign/index.html.twig', [
            'campaigns' => $campaignRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app.admin.campaign.new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $campaign = new Campaign();
        $form = $this->createForm(CampaignType::class, $campaign);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($campaign);
            $entityManager->flush();

            return $this->redirectToRoute('app.admin.campaign');
        }

        return $this->render('admin/admin_campaign/new.html.twig', [
            'campaign' => $campaign,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app.admin.campaign.show', methods: ['GET'])]
    public function show(Campaign $campaign): Response
    {
        return $this->render('admin/admin_campaign/show.html.twig', [
            'campaign' => $campaign,
        ]);
    }

    #[Route('/{id}/edit', name: 'app.admin.campaign.edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Campaign $campaign): Response
    {
        $form = $this->createForm(CampaignType::class, $campaign);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('app.admin.campaign');
        }

        return $this->render('admin/admin_campaign/edit.html.twig', [
            'campaign' => $campaign,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app.admin.campaign.delete', methods: ['POST'])]
    public function delete(Request $request, Campaign $campaign): Response
    {
        if ($this->isCsrfTokenValid('delete'.$campaign->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($campaign);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app.admin.campaign');
    }
}
