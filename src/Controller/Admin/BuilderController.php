<?php

namespace App\Controller\Admin;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ODM\MongoDB\DocumentManager;
use App\Document\Site;
use App\Document\Page;

class BuilderController extends AbstractController
{
    private $documentManager;

    public function __construct(DocumentManager $documentManager)
    {
        $this->documentManager = $documentManager;
    }

    public function buildSite(Request $request, Site $site): ?Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $pages = $this->documentManager->getRepository(Page::class)->findBy(array('site' => $site, 'user' => $this->getUser()), array('order' => 'ASC'));

        return $this->render(
            'Admin/Site/page_list.html.twig',
            array(
                'pages' => $pages,
                'site' => $site,
            )
        );
    }
}
