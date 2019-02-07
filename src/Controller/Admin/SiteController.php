<?php

namespace App\Controller\Admin;

use App\Document\Page;
use phpDocumentor\Reflection\Types\This;
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
        $qb = $this->documentManager->createQueryBuilder(Site::class);
        $qb->addAnd($qb->expr()->field('user')->equals($this->getUser()));
        $qb->addAnd($qb->expr()->field('deleted')->notEqual(true));
        $sites = $qb->getQuery()->execute();

        return $this->render(
            'Admin/site_list.html.twig',
            [
                'sites' => $sites,
            ]
        );
    }

    public function create(Request $request): Response
    {
        // todo: copy pages from template
        $site = new Site();
        $form = $this->createForm(SiteType::class, $site);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $site->setUser($this->getUser());

            $templateSite = $this->documentManager->getRepository(Site::class)->findOneBy(['isTemplate' => true]);
            $templatePages = $this->documentManager->getRepository(Page::class)->findBy(['site' => $templateSite]);

            $this->documentManager->persist($site);
            $this->documentManager->flush();

            /** @var Page $page */
            foreach ($templatePages as $page) {
                $pageCopy = new Page();
                $pageCopy->setName($page->getName());
                $pageCopy->setSite($site);
                $pageCopy->setUser($this->getUser());
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

            return $this->redirectToRoute('user_admin_site_list');
        }

        return $this->render(
            'Admin/site_edit.html.twig',
            [
                'form' => $form->createView(),
            ]
        );
    }

    public function edit(Request $request, Site $site): ?Response
    {
        $form = $this->createForm(SiteType::class, $site);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->documentManager->persist($site);
            $this->documentManager->flush();

            return $this->redirectToRoute('user_admin_site_list');
        }

        return $this->render(
            'Admin/site_edit.html.twig',
            [
                'form' => $form->createView(),
            ]
        );
    }

    public function delete(Request $request, Site $site): ?Response
    {
        $site->setActive(false);
        $site->setDeleted(true);
        $this->documentManager->flush();

        return $this->redirectToRoute('user_admin_site_list');
    }
}
