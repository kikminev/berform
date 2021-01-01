<?php

namespace App\Controller;

use App\Repository\UserSite\SiteRepository;
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
}
