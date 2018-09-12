<?php

namespace App\Controller\Admin;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ODM\MongoDB\DocumentManager;
use App\Document\Site;
use App\Document\Page;
use App\Form\Admin\PageType;
use Symfony\Component\Routing\Annotation\Route;

class PageController extends AbstractController
{
    private $documentManager;

    public function __construct(DocumentManager $documentManager)
    {
        $this->documentManager = $documentManager;
    }

    public function edit(Request $request, Page $page): ?Response
    {
        $form = $this->createForm(PageType::class, $page);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->documentManager->persist($page);
            $this->documentManager->flush();
        }

        return $this->render(
            'Admin/page_edit.html.twig',
            array(
                'form' => $form->createView(),
            )
        );
    }
}
