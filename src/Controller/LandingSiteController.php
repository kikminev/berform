<?php

namespace App\Controller;

use App\Repository\SiteRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ODM\MongoDB\DocumentManager;
use App\Document\Site;
use App\Document\Page;

class LandingSiteController extends AbstractController
{
    public function __construct(DocumentManager $dm)
    {
        $this->dm = $dm;
    }

    public function home(SiteRepository $siteRepository)
    {
        return $this->render(
            'LandingSite/index.html.twig',
            ['templates' => $siteRepository->getTemplates()]
        );
    }
}
