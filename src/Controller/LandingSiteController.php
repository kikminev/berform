<?php

namespace App\Controller;

use App\Repository\SiteRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ODM\MongoDB\DocumentManager;
use App\Document\User;
use App\Form\UserType;

class LandingSiteController extends AbstractController
{
    private $dm;

    public function __construct(DocumentManager $dm)
    {
        $this->dm = $dm;
    }

    public function home(SiteRepository $siteRepository)
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user, ['action' => $this->generateUrl('app_registration')]);

        return $this->render(
            'LandingSite/index.html.twig',
            [
                'templates' => $siteRepository->getTemplates(),
                'form' => $form->createView(),
            ]
        );
    }
}
