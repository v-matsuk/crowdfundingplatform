<?php

namespace App\Controller;

use App\Entity\Gallery;
use App\Form\GalleryType;
use App\Service\ImageUploader;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/campaign")
 */
class GalleryController extends AbstractController
{
    /**
     * @var ImageUploader
     */
    private $imageUploader;

    public function __construct(ImageUploader $imageUploader){
        $this->imageUploader = $imageUploader;
    }
    /**
     * @Route("/{id}/gallery/{id2}/edit", name="app.gallery.edit", requirements={"id": "[0-9]+", "id2": "[0-9]+"})
     */
    public function edit(Request $request, $id, $id2):Response
    {
        $postRep = $this->getDoctrine()->getRepository(Gallery::class);
        $gallery = $postRep->findOneBy(['id' => $id2]);
        $form = $this->createForm(GalleryType::class, $gallery);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $file1 = $form['file1']->getData();
            if ($file1){
                $gallery->setImage1($this->imageUploader->uploadImageToCloudinary($file1));
            }
            $file2 = $form['file2']->getData();
            if ($file2){
                $gallery->setImage2($this->imageUploader->uploadImageToCloudinary($file2));
            }
            $file3 = $form['file3']->getData();
            if ($file3){
                $gallery->setImage3($this->imageUploader->uploadImageToCloudinary($file3));
            }
            $file4 = $form['file4']->getData();
            if ($file4){
                $gallery->setImage4($this->imageUploader->uploadImageToCloudinary($file4));
            }
            $file5 = $form['file5']->getData();
            if ($file5){
                $gallery->setImage5($this->imageUploader->uploadImageToCloudinary($file5));
            }
            $file6 = $form['file6']->getData();
            if ($file6){
                $gallery->setImage6($this->imageUploader->uploadImageToCloudinary($file6));
            }
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($gallery);
            $entityManager->flush();
            return $this->redirectToRoute('app.campaign', array('id'=>$id));
        }

        return $this->render('gallery/edit.html.twig', [
            'gallery' =>$gallery,
            'form' => $form->createView(),
            'id'=>$id,
        ]);
    }
}
