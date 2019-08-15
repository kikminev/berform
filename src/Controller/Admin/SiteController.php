<?php

namespace App\Controller\Admin;

use App\Document\Page;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ODM\MongoDB\DocumentManager;
use App\Document\Site;
use App\Form\Admin\SiteType;

class SiteController extends AbstractController
{
    /**
     * @return Response
     * @throws \Doctrine\ODM\MongoDB\MongoDBException
     */
    public function list(DocumentManager $documentManager)
    {
        $qb = $documentManager->createQueryBuilder(Site::class);
        $qb->addAnd($qb->expr()->field('user')->equals($this->getUser()));
        $qb->addAnd($qb->expr()->field('deleted')->notEqual(true));
        $sites = $qb->getQuery()->execute();

        return $this->render(
            'admin/site_list.html.twig',
            [
                'sites' => $sites,
            ]
        );
    }

    public function create(Request $request, ParameterBagInterface $param, DocumentManager $documentManager): Response
    {
        // todo: copy pages from template

        $site = new Site();
        $supportedLanguages = $param->get('supported_languages');
        $form = $this->createForm(SiteType::class, $site, ['supported_languages' => $supportedLanguages]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $site->setUser($this->getUser());

            $templateSite = $documentManager->getRepository(Site::class)->findOneBy(['isTemplate' => true]);
            $templatePages = $documentManager->getRepository(Page::class)->findBy(['site' => $templateSite]);

            $documentManager->persist($site);
            $documentManager->flush();

            /** @var Page $page */
            foreach ($templatePages as $page) {
                $pageCopy = new Page();
                $pageCopy->setName($page->getName());
                $pageCopy->setSite($site);
                $pageCopy->setUser($this->getUser());
                $pageCopy->setActive(true);
                $pageCopy->setOrder($page->getOrder());
                $pageCopy->setSlug($page->getSlug());
                $pageCopy->setLocale($page->getLocale());
                $pageCopy->setUpdatedAt(new \DateTime());
                $pageCopy->setCreatedAt(new \DateTime());

                $documentManager->persist($pageCopy);
            }

            $documentManager->flush();

            return $this->redirectToRoute('admin');
        }

        return $this->render(
            'Admin/site_edit.html.twig',
            [
                'form' => $form->createView(),
            ]
        );
    }

    public function edit(Request $request, Site $site, ParameterBagInterface $param, DocumentManager $documentManager): ?Response
    {
        $this->denyAccessUnlessGranted('edit', $site);

        $supportedLanguages = $param->get('supported_languages');
        $form = $this->createForm(SiteType::class, $site);
        $form->handleRequest($request);

        // todo: move this to transformer
        if (!$form->isSubmitted()) {
            $currentTranslatedAddress = $site->getTranslatedAddress();
            foreach ($supportedLanguages as $language) {
                $translatedAddress = isset($currentTranslatedAddress[$language]) ? $currentTranslatedAddress[$language] : '';
                $form->get('address_' . $language)->setData($translatedAddress);
            }
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $translatedSiteAddress = [];
            foreach ($supportedLanguages as $language) {
                $translatedSiteAddress[$language] = $form['address_' . $language]->getData();
            }

            $site->setTranslatedAddress($translatedSiteAddress);
            $documentManager->persist($site);
            $documentManager->flush();

            return $this->redirectToRoute('admin');
        }

        return $this->render(
            'Admin/site_edit.html.twig',
            [
                'form' => $form->createView(),
            ]
        );
    }

    public function delete(Site $site, DocumentManager $documentManager): ?Response
    {
        $this->denyAccessUnlessGranted('edit', $site);

        $site->setActive(false);
        $site->setDeleted(true);
        $documentManager->flush();

        return $this->redirectToRoute('admin');
    }
}
