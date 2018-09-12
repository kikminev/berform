<?php

namespace App\Controller\Admin;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ODM\MongoDB\DocumentManager;
use App\Document\Site;
use App\Form\Admin\SiteType;
use Symfony\Component\Routing\Annotation\Route;

class SiteController extends AbstractController
{
    private $documentManager;

    public function __construct(DocumentManager $documentManager)
    {
        $this->documentManager = $documentManager;
    }

    public function list()
    {
        $sites = $this->documentManager->getRepository(Site::class)->findAll();

        return $this->render(
            'Admin/site_list.html.twig',
            array(
                'sites' => $sites,
            )
        );
    }

    public function create(Request $request): Response
    {
        $site = new Site();
        $form = $this->createForm(SiteType::class, $site);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->documentManager->persist($site);
            $this->documentManager->flush();

            return $this->redirectToRoute('admin_site_list');
        }

        return $this->render(
            'Admin/site_edit.html.twig',
            array(
                'form' => $form->createView(),
            )
        );
    }

    public function edit(Request $request, Site $site): ?Response
    {
        $form = $this->createForm(SiteType::class, $site);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->documentManager->persist($site);
            $this->documentManager->flush();

            return $this->redirectToRoute('admin_site_list');
        }

        return $this->render(
            'Admin/site_edit.html.twig',
            array(
                'form' => $form->createView(),
            )
        );
    }

    public function delete(Request $request, Site $site): ?Response
    {
        $site->setActive(false);
        $site->setDeleted(true);
        $this->documentManager->flush();

        return $this->redirectToRoute('admin_site_list');
    }
}
