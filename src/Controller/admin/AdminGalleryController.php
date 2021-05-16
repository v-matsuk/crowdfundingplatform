<?php

namespace App\Controller\admin;

use App\Entity\Gallery;
use App\Form\Gallery1Type;
use App\Repository\GalleryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/gallery')]
class AdminGalleryController extends AbstractController
{
    #[Route('/', name: 'app.admin.gallery', methods: ['GET'])]
    public function index(GalleryRepository $galleryRepository): Response
    {
        return $this->render('admin/admin_gallery/index.html.twig', [
            'galleries' => $galleryRepository->findAll(),
        ]);
    }


    #[Route('/{id}/edit', name: 'app.admin.gallery.edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Gallery $gallery): Response
    {
        $form = $this->createForm(Gallery1Type::class, $gallery);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('app.admin.gallery');
        }

        return $this->render('admin/admin_gallery/edit.html.twig', [
            'gallery' => $gallery,
            'form' => $form->createView(),
        ]);
    }
}
