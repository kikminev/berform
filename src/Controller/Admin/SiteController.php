<?php

namespace App\Controller\Admin;

use App\Document\Page;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ODM\MongoDB\DocumentManager;
use App\Document\Site;
use App\Form\Admin\SiteType;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\SiteRepository;
use Symfony\Component\Validator\Constraints\Date;

class SiteController extends AbstractController
{
    private $documentManager;

    public function __construct(DocumentManager $documentManager)
    {
        $this->documentManager = $documentManager;
    }

    public function list()
    {
        // todo: find pages for specifix user
        $sites = $this->documentManager->getRepository(Site::class)->findAll();
        $qb = $this->documentManager->createQueryBuilder(Site::class);
        $qb->addOr($qb->expr()->field('deleted')->notEqual(true));
        $sites = $qb->getQuery()->execute();

        //$sites = $this->documentManager->getRepository(Site::class)->field

        return $this->render(
            'Admin/site_list.html.twig',
            array(
                'sites' => $sites,
            )
        );
    }

    public function create(Request $request): Response
    {
        // todo: copy pages from template
        $site = new Site();
        $form = $this->createForm(SiteType::class, $site);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $templateSite = $this->documentManager->getRepository(Site::class)->findOneBy(['isTemplate' => true]);
            $templatePages = $this->documentManager->getRepository(Page::class)->findBy(['site' => $templateSite]);

            $this->documentManager->persist($site);
            $this->documentManager->flush();

            /** @var Page $page */
            foreach ($templatePages as $page) {
                $pageCopy = new Page();
                $pageCopy->setName($page->getName());
                $pageCopy->setSite($site);
                $pageCopy->setActive(true);
                $pageCopy->setOrder($page->getOrder());
                $pageCopy->setContent($page->getContent());
                $pageCopy->setSlug($page->getSlug());
                $pageCopy->setKeywords($page->getKeywords());
                $pageCopy->setLocale($page->getLocale());
                $pageCopy->setUpdatedAt(new \DateTime());
                $pageCopy->setCreatedAt(new \DateTime());

                $this->documentManager->persist($pageCopy);
            }

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
