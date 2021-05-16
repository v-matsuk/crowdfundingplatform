<?php

namespace App\Controller;

use App\Entity\Campaign;
use App\Entity\Post;
use App\Form\PostType;
use App\Form\RemoveImageType;
use App\Service\ImageUploader;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class PostController
 * @package App\Controller
 * @Route ("campaign/")
 */
class PostController extends AbstractController
{
    /**
     * @var ImageUploader
     */
    private $imageUploader;

    public function __construct(ImageUploader $imageUploader){
        $this->imageUploader = $imageUploader;
    }
    /**
     * @Route("/{id}/post/new", name="app.post.new", requirements={"id": "[0-9]+"})
     */
    public function new(Request $request, $id): Response
    {
        $post = new Post();
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $campRep = $this->getDoctrine()->getRepository(Campaign::class);
            $campaign = $campRep->findOneBy(['id' => $id]);
            $campaign ->setDate();
            $post->setCampaign($campaign);
            $file = $form['file']->getData();
            if ($file){
                $post->setImage($this->imageUploader->uploadImageToCloudinary($file));
            }
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($post);
            $entityManager->persist($campaign);
            $entityManager->flush();

            return $this->redirectToRoute('app.campaign', array('id'=>$id));
        }

        return $this->render('post/new.html.twig', [
            'post' => $post,
            'form' => $form->createView(),
            'id'=>$id,
        ]);
    }
    /**
     * @Route("/{id}/post/{id2}/edit", name="app.post.edit", requirements={"id": "[0-9]+", "id2": "[0-9]+"})
     */
    public function edit(Request $request, $id, $id2):Response
    {
        $postRep = $this->getDoctrine()->getRepository(Post::class);
        $post = $postRep->findOneBy(['id' => $id2]);
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $file = $form['file']->getData();
            if ($file){
                $post->setImage($this->imageUploader->uploadImageToCloudinary($file));
            }
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($post);
            $entityManager->flush();
            return $this->redirectToRoute('app.campaign', array('id'=>$id));
        }

        return $this->render('post/edit.html.twig', [
            'post' => $post,
            'form' => $form->createView(),
            'id'=>$id,
            'id2'=>$id2
        ]);
    }

    #[Route('//post/{id}/delete', name: 'app.post.delete', methods: ['POST'])]
    public function delete(Request $request, Post $post): Response
    {
        if ($this->isCsrfTokenValid('delete'.$post->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($post);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app.campaign', array('id'=>$post->getCampaign()->getId()));
    }
    /**
     * @Route("/{id}/post/{id2}/remove", name="app.post.remove", requirements={"id": "[0-9]+", "id2": "[0-9]+"})
     */
    public function remove(Request $request, Post $post, $id, $id2):Response
    {
        $form = $this->createForm(RemoveImageType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $toRemove = $form['toRemove']->getData();
            if($toRemove){
                $post->setImage(null);
            }
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($post);
            $entityManager->flush();

            return $this->redirectToRoute('app.post.edit', array('id'=>$id, 'id2'=>$id2));
        }

        return $this->render('post/remove.html.twig', [
            'post' =>$post,
            'form' => $form->createView(),
            'id'=>$id,
            'id2'=>$id2
        ]);
    }
}
