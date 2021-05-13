<?php

namespace App\Controller;

use App\Entity\Campaign;
use App\Entity\Theme;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ThemeController extends AbstractController
{
    #[Route('/theme/{id}', name: 'app.theme')]
    public function index($id): Response
    {
        $themeRep = $this->getDoctrine()->getRepository(Theme::class);
        $theme = $themeRep->findOneBy(['id' => $id]);
        $campRep = $this->getDoctrine()->getRepository(Campaign::class);
        $campaigns = $campRep->findBy(['theme' => $id]);
        return $this->render('theme/theme.html.twig', [
            'theme' => $theme,
            'campaigns'=>$campaigns,
        ]);
    }
}
