<?php

namespace App\Controller\Admin;

use App\Repository\FileRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ODM\MongoDB\DocumentManager;
use App\Document\Site;
use App\Document\Page;
use App\Document\File;
use App\Form\Admin\PageType;
use Symfony\Component\Routing\Annotation\Route;

class PageController extends AbstractController
{
    private $documentManager;

    // todo: import repositories with auto-wiring
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

        $uploadedFiles = $this->documentManager->getRepository(File::class)->findBy(['page' => $page], ['order' => 'DESC ']);

        return $this->render(
            'Admin/page_edit.html.twig',
            [
                'form' => $form->createView(),
                'uploadedFiles' => $uploadedFiles,
                'page' => $page,
            ]
        );
    }
}
