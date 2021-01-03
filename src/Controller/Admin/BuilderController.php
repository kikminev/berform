<?php

namespace App\Controller\Admin;

use App\Entity\Site;
use App\Repository\PageRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ODM\MongoDB\DocumentManager;

class BuilderController extends AbstractController
{
    private $documentManager;

    public function __construct(DocumentManager $documentManager)
    {
        $this->documentManager = $documentManager;
    }

    public function buildSite(PageRepository $pageRepository, Site $site): ?Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        $this->denyAccessUnlessGranted('modify', $site);

        $pages = $pageRepository->findActiveByUserSite($this->getUser(), $site);

        return $this->render(
            'Admin/Site/page_list.html.twig',
            array(
                'pages' => $pages,
                'site' => $site,
            )
        );
    }
}


