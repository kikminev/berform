<?php

namespace App\Controller\Admin;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ODM\MongoDB\DocumentManager;
use App\Document\Site;
use App\Document\Page;
use App\Form\Admin\SiteType;
use Symfony\Component\Routing\Annotation\Route;

class BuilderController extends AbstractController
{
    private $documentManager;

    public function __construct(DocumentManager $documentManager)
    {
        $this->documentManager = $documentManager;
    }

    public function buildSite(Request $request, Site $site): ?Response
    {
        $pages = $this->documentManager->getRepository(Page::class)->findBy(array(), array('order' => 'DESC '));
        // get all pages
        // generate ajax link to all pages
        // return a form that edits pages
        // create a

        return $this->render(
            'Admin/builder.html.twig',
            array(
                'pages' => $pages,
            )
        );
    }
}
