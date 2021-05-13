<?php

namespace App\Controller\admin;

use App\Entity\Bonus;
use App\Form\Bonus1Type;
use App\Repository\BonusRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/bonus')]
class AdminBonusController extends AbstractController
{
    #[Route('/', name: 'app.admin.bonus', methods: ['GET'])]
    public function index(BonusRepository $bonusRepository): Response
    {
        return $this->render('admin/admin_bonus/index.html.twig', [
            'bonuses' => $bonusRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app.admin.bonus.new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $bonus = new Bonus();
        $form = $this->createForm(Bonus1Type::class, $bonus);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($bonus);
            $entityManager->flush();

            return $this->redirectToRoute('app.admin.bonus');
        }

        return $this->render('admin/admin_bonus/new.html.twig', [
            'bonus' => $bonus,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app.admin.bonus.show', methods: ['GET'])]
    public function show(Bonus $bonus): Response
    {
        return $this->render('admin/admin_bonus/show.html.twig', [
            'bonus' => $bonus,
        ]);
    }

    #[Route('/{id}/edit', name: 'app.admin.bonus.edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Bonus $bonus): Response
    {
        $form = $this->createForm(Bonus1Type::class, $bonus);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('app.admin.bonus');
        }

        return $this->render('admin/admin_bonus/edit.html.twig', [
            'bonus' => $bonus,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app.admin.bonus.delete', methods: ['POST'])]
    public function delete(Request $request, Bonus $bonus): Response
    {
        if ($this->isCsrfTokenValid('delete'.$bonus->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($bonus);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app.admin.bonus');
    }
}
