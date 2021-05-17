<?php

namespace App\Controller;

use App\Entity\Bonus;
use App\Entity\Comment;
use App\Entity\Campaign;
use App\Entity\Gallery;
use App\Entity\Payment;
use App\Entity\Post;
use App\Entity\Rating;
use App\Entity\User;
use App\Form\CampaignUType;
use App\Form\CommentType;
use App\Form\PaymentType;
use App\Form\PayType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/campaign")
 */
class CampaignController extends AbstractController
{
    /**
     * @Route("/{id}", name="app.campaign", requirements={"id": "[0-9]+"})
     */
    public function index(Request $request, $id): Response
    {
        $campRep = $this->getDoctrine()->getRepository(Campaign::class);
        $campaign = $campRep->findOneBy(['id' => $id]);
        $bonusRep = $this->getDoctrine()->getRepository(Bonus::class);
        $bonuses = $bonusRep->findBy(
            ['campaign' => $id],
            ['price' => 'ASC']
        );
        $galleryRep = $this->getDoctrine()->getRepository(Gallery::class);
        $gallery = $galleryRep->findOneBy(['campaign' => $id]);
        $postRep = $this->getDoctrine()->getRepository(Post::class);
        $posts = $postRep->findBy(['campaign' => $id]);
        $ratRep = $this->getDoctrine()->getRepository(Rating::class);
        $ratings = $ratRep->findBy(['campaign'=> $id]);
        $users = [];
        for ($i=0; $i < count($ratings); $i++){
            $users[$i] = $ratings[$i]->getUser();
        }
        $isRated = in_array($this->get('security.token_storage')->getToken()->getUser(), $users);
        $commentRep = $this->getDoctrine()->getRepository(Comment::class);
        $comments = $commentRep->findBy(['campaign' => $id]);
        //
        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);
        //
        if ($form->isSubmitted() && $form->isValid()) {
            $campRep = $this->getDoctrine()->getRepository(Campaign::class);
            $campaign = $campRep->findOneBy(['id' => $id]);
            $comment->setCampaign($campaign);
            $user = $this->get('security.token_storage')->getToken()->getUser();
            $comment->setUser($user);
            $comment->setDate();
            $comment->setRating(0);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($comment);
            $entityManager->flush();

            return $this->redirectToRoute('app.campaign', array('id'=>$id));
        }
        //
        return $this->render('campaign/campaign.html.twig', [
            'campaign' => $campaign,
            'isRated' => $isRated,
            'bonuses' => $bonuses,
            'gallery' => $gallery,
            'posts' => $posts,
            'comments' => $comments,
            'comment' => $comment,
            'form' => $form->createView(),
        ]);
    }
    /**
     * @Route("/new/{id}", name="app.campaign.new")
     */
    public function new(Request $request, $id): Response
    {
        $campaign = new Campaign();
        $form = $this->createForm(CampaignUType::class, $campaign);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $userRep = $this->getDoctrine()->getRepository(User::class);
            $user = $userRep->findOneBy(['id' => $id]);
            $campaign->setUser($user);
            $campaign->setRating(0);
            $campaign->setNumberOfRatings(0);
            $campaign->setCurrentMoney(0);
            $gallery = new Gallery();
            $gallery->setCampaign($campaign);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($campaign);
            $entityManager->persist($gallery);
            $entityManager->flush();

            return $this->redirectToRoute('app.profile', array('id'=>$id));
        }

        return $this->render('campaign/new.html.twig', [
            'campaign' => $campaign,
            'form' => $form->createView(),
            'id' => $id,
        ]);
    }
    /**
     * @Route("/{id}/edit", name="app.campaign.edit", requirements={"id": "[0-9]+"})
     */
    public function edit(Request $request, Campaign $campaign, $id):Response
    {
        $form = $this->createForm(CampaignUType::class, $campaign);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            return $this->redirectToRoute('app.profile', array('id'=>$id));
        }

        return $this->render('campaign/edit.html.twig', [
            'campaign' => $campaign,
            'form' => $form->createView(),
            'id' => $id,
        ]);
    }
    #[Route('/{id}/delete', name: 'app.campaign.delete', methods: ['POST'])]
    public function delete(Request $request, Campaign $campaign): Response
    {
        $id = $campaign->getUser()->getId();
        if ($this->isCsrfTokenValid('delete'.$campaign->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($campaign);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app.profile', array('id'=>$id));
    }
    /**
     *@Route("/{id}/show", name="app.campaign.show", requirements={"id": "[0-9]+"})
     */
    public function show(Campaign $campaign): Response
    {
        return $this->render('campaign/show.html.twig', [
            'campaign' => $campaign,
        ]);
    }

    //donate

    /**
     * @Route("/{id}/donate/new", name="app.donate.new", requirements={"id": "[0-9]+"})
     */
    public function donateNew(Request $request, $id): Response
    {
        $payment = new Payment();
        $form = $this->createForm(PaymentType::class, $payment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $campRep = $this->getDoctrine()->getRepository(Campaign::class);
            $campaign = $campRep->findOneBy(['id' => $id]);
            $campaign->setCurrentMoney($campaign->getCurrentMoney()+$payment->getAmount());
            $user = $this->get('security.token_storage')->getToken()->getUser();
            $payment->setUser($user);
            $bonusRep = $this->getDoctrine()->getRepository(Bonus::class);
            $bonuses = $bonusRep->findBy(['campaign' => $id]);
            $info = [];
            for ($i = 0; $i < count($bonuses); $i++) {
                $info[$i] = array($bonuses[$i]->getPrice()-$payment->getAmount(), $i) ;
            }
            arsort($info);
            foreach ($info as $item){
                if ($item[0]<=0){
                    $payment->setName($bonuses[$item[1]]->getName());
                    $payment->setDescription($bonuses[$item[1]]->getDescription());
                    break;
                }
            }
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($payment);
            $entityManager->flush();

            return $this->redirectToRoute('app.campaign', array('id'=>$id));
        }

        return $this->render('donate/new.html.twig', [
            'payment' => $payment,
            'form' => $form->createView(),
            'id'=>$id,
        ]);
    }

    /**
     * @Route("/{id}/donate/{id2}", name="app.donate.current",
     *     requirements={"id": "[0-9]+", "id2": "[0-9]+"})
     */
    public function donateCurrent(Request $request, $id, $id2): Response
    {
        $payment = new Payment();
        $bonusRep = $this->getDoctrine()->getRepository(Bonus::class);
        $bonus = $bonusRep->findOneBy(['id' => $id2]);
        $form = $this->createForm(PayType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted()){
            $payment->setAmount($bonus->getPrice());
            $payment->setName($bonus->getName());
            $payment->setDescription($bonus->getDescription());
            $campRep = $this->getDoctrine()->getRepository(Campaign::class);
            $user = $this->get('security.token_storage')->getToken()->getUser();
            $payment->setUser($user);
            $campaign = $campRep->findOneBy(['id' => $id]);
            $campaign->setCurrentMoney($campaign->getCurrentMoney()+$payment->getAmount());
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($payment);
            $entityManager->flush();

            return $this->redirectToRoute('app.campaign', array('id' => $id));
        }

        return $this->render('donate/submit.html.twig', [
            'form' => $form->createView(),
            'bonus'=> $bonus,
            'id'=>$id,
        ]);
    }
}
