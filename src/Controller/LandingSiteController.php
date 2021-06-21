<?php

namespace App\Controller;

use App\Repository\SiteRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class LandingSiteController extends AbstractController
{
    public function __construct()
    {
    }

    public function home(SiteRepository $siteRepository)
    {
        return $this->render(
            'LandingSite/index.html.twig',
            [
                'templates' => $siteRepository->getTemplates()
            ]
        );
    }

    public function page(String $slug)
    {
        return $this->render(
            "LandingSite/{$slug}.html.twig",
            [
            ]
        );
    }
}
