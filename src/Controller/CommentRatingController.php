<?php

namespace App\Controller;


use App\Entity\Comment;
use App\Entity\CommentRating;
use App\Form\CommentRatingType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CommentRatingController extends AbstractController
{
    /**
     * @Route("/{id}/comment/{id2}/like", name="app.comment.like", requirements={"id": "[0-9]+", "id2": "[0-9]+"})
     */
    public function like(Request $request, Comment $comment, $id, $id2): Response
    {
        $commentRRep = $this->getDoctrine()->getRepository(CommentRating::class);
        $commentR = $commentRRep->findBy(['comment' => $id2]);
        $users = [];
        for ($i=0; $i < count($commentR); $i++){
            $users[$i] = $commentR[$i]->getUser();
        }
        if (in_array($this->get('security.token_storage')->getToken()->getUser(), $users)){
            return $this->redirectToRoute('app.campaign', array('id'=>$id));
        }
        $commentRating = new CommentRating();
        $form = $this->createForm(CommentRatingType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->get('security.token_storage')->getToken()->getUser();
            $commentRating->setUser($user);
            $commentRating->setComment($comment);
            $commentRating->setRating(1);
            $comment->setRating($comment->getRating()+1);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($comment);
            $entityManager->persist($commentRating);
            $entityManager->flush();

            return $this->redirectToRoute('app.campaign', array('id'=>$id));
        }

        return $this->render('comment_rating/like.html.twig', [
            'comment' => $comment,
            'form' => $form->createView(),
            'id' => $id,
        ]);
    }

    /**
     * @Route("/{id}/comment/{id2}/dislike", name="app.comment.dislike", requirements={"id": "[0-9]+", "id2": "[0-9]+"})
     */
    public function dislike(Request $request, Comment $comment ,$id, $id2): Response
    {
        $commentRRep = $this->getDoctrine()->getRepository(CommentRating::class);
        $commentR = $commentRRep->findBy(['comment' => $id2]);
        $users = [];
        for ($i=0; $i < count($commentR); $i++){
            $users[$i] = $commentR[$i]->getUser();
        }
        if (in_array($this->get('security.token_storage')->getToken()->getUser(), $users)){
            return $this->redirectToRoute('app.campaign', array('id'=>$id));
        }
        $commentRating = new CommentRating();
        $form = $this->createForm(CommentRatingType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->get('security.token_storage')->getToken()->getUser();
            $commentRating->setUser($user);
            $commentRating->setComment($comment);
            $commentRating->setRating(-1);
            $comment->setRating($comment->getRating()-1);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($comment);
            $entityManager->persist($commentRating);
            $entityManager->flush();

            return $this->redirectToRoute('app.campaign', array('id'=>$id));
        }

        return $this->render('comment_rating/like.html.twig', [
            'comment' => $comment,
            'form' => $form->createView(),
            'id' => $id,
        ]);
    }
}
