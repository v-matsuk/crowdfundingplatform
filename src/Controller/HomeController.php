<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Campaign;
use App\Entity\Tag;
use App\Entity\User;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\DateTime;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app.home')]
    public function index(): Response
    {
        $campRep = $this->getDoctrine()->getRepository(Campaign::class);
        $campaignsD = $campaignsR = $campRep->findAll();
        usort($campaignsD, function($a, $b) {
            $ad = new DateTime($a->getDate());
            $bd = new DateTime($b->getDate());
            if ($ad === $bd) {
                return 0;
            }
            return $ad < $bd ? 1 : -1;
        });
        usort($campaignsR, function($a, $b) {
            if ($a->getRating() === $b->getRating()) {
                return 0;
            }
            return $a->getRating() < $b->getRating() ? 1 : -1;
         });
        $tagRep = $this->getDoctrine()->getRepository(Tag::class);
        $tags = $tagRep->findAll();
        return $this->render('home/home.html.twig', [
            'campaignsD' => $campaignsD,
            'campaignsR' => $campaignsR,
            'tags'=>$tags,
        ]);
    }
}
