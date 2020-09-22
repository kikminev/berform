<?php

namespace App\Controller\Admin;

use App\Document\Page;
use App\Repository\DomainRepository;
use App\Repository\SiteRepository;
use App\Service\Domain\DomainResolver;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
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
    public function list(SiteRepository $siteRepository)
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $user = $this->getUser();
        $sites = $siteRepository->getByUser($user);

        return $this->render(
            'Admin/Site/site_list.html.twig',
            [
                'sites' => $sites,
            ]
        );
    }

    public function create(Request $request, ParameterBagInterface $param, DocumentManager $documentManager, DomainRepository $domainRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        // todo: copy pages from template

        $domains = $domainRepository->findActiveByUser($this->getUser());

        $site = new Site();
        $supportedLanguages = $param->get('supported_languages');
        $form = $this->createForm(SiteType::class, $site, ['supported_languages' => $supportedLanguages, 'active_domains' => $domains]);
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
        $this->denyAccessUnlessGranted('ROLE_USER');
        $this->denyAccessUnlessGranted('modify', $site);

        $supportedLanguages = $param->get('supported_languages');
        $siteActivatedLanguages = array_filter($param->get('supported_languages'), function($language) use ($site) {
            return in_array($language, $site->getSupportedLanguages(), false);
        });

        $form = $this->createForm(SiteType::class, $site, ['supported_languages' => $siteActivatedLanguages]);
        $form->handleRequest($request);

        // todo: move this to transformer
        if (!$form->isSubmitted()) {
            $currentTranslatedAddress = $site->getTranslatedAddress();
            foreach ($supportedLanguages as $language) {
                if(in_array($language, $siteActivatedLanguages)) {
                    $translatedAddress = isset($currentTranslatedAddress[$language]) ? $currentTranslatedAddress[$language] : '';
                    $form->get('address_' . $language)->setData($translatedAddress);
                }
            }
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $translatedSiteAddress = [];
            foreach ($supportedLanguages as $language) {
                if(in_array($language, $siteActivatedLanguages)) {
                    $translatedSiteAddress[$language] = $form['address_' . $language]->getData();
                }
            }

            $site->setTranslatedAddress($translatedSiteAddress);
            $documentManager->persist($site);
            $documentManager->flush();

            return $this->redirectToRoute('admin');
        }

        return $this->render(
            'Admin/site_edit.html.twig',
            [
                'site' => $site,
                'form' => $form->createView(),
            ]
        );
    }

    public function delete(Site $site, DocumentManager $documentManager): ?Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        $this->denyAccessUnlessGranted('modify', $site);

        $site->setActive(false);
        $site->setDeleted(true);
        $documentManager->flush();

        return $this->redirectToRoute('admin');
    }

    public function redirectToSite(Site $site, DomainResolver $domainResolver): RedirectResponse
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        $this->denyAccessUnlessGranted('modify', $site);

        return $domainResolver->getSiteReachableDomain($site);
    }
}
