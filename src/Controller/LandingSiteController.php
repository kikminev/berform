<?php

namespace App\Controller;

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

    public function home()
    {
        return $this->render(
            'LandingSite/index.html.twig'
        );
    }
}
