<?php

namespace App\Controller;

use App\Entity\Tag;
use App\Form\TagType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TagController extends AbstractController
{
    /**
     * @Route("/tag/{id}", name="app.tag", requirements={"id": "[0-9]+"})
     */
    public function index($id): Response
    {
        $tagRep = $this->getDoctrine()->getRepository(Tag::class);
        $tag = $tagRep->findOneBy(['id' => $id]);
        return $this->render('tag/tag.html.twig', [
            'tag'=>$tag,
        ]);
    }
    /**
     * @Route("/tag/new/{id}", name="app.tag.new", requirements={"id": "[0-9]+"})
     */
    public function new(Request $request, $id): Response
    {
        $tag = new tag();
        $form = $this->createForm(TagType::class, $tag);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($tag);
            $entityManager->flush();

            return $this->redirectToRoute('app.campaign.new', array('id'=>$id));
        }

        return $this->render('tag/new.html.twig', [
            'form'=>$form->createView(),
            'tag'=>$tag,
            'id'=>$id
        ]);
    }
    /**
     * @Route("/tag/edit/{id}", name="app.tag.edit", requirements={"id": "[0-9]+"})
     */
    public function edit(Request $request, $id): Response
    {
        $tag = new tag();
        $form = $this->createForm(TagType::class, $tag);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($tag);
            $entityManager->flush();

            return $this->redirectToRoute('app.campaign.edit', array('id'=>$id));
        }

        return $this->render('tag/new.html.twig', [
            'form'=>$form->createView(),
            'tag'=>$tag,
            'id'=>$id
        ]);
    }
}
