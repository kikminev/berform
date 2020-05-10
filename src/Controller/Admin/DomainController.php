<?php

namespace App\Controller\Admin;

use App\DNS\CloudflareDnsUpdater;
use App\Document\User;
use App\Form\Admin\DomainType;
use App\Repository\DomainRepository;
use App\Repository\SiteRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Document\Domain;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ODM\MongoDB\DocumentManager;

class DomainController extends AbstractController
{
    /**
     * @param DomainRepository $domainRepository
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Doctrine\ODM\MongoDB\MongoDBException
     */
    public function list(DomainRepository $domainRepository)
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $domains = $domainRepository->findByUser($this->getUser());

        return $this->render(
            'Admin/Domain/domain_list.html.twig',
            [
                'domains' => $domains,
            ]
        );
    }

    public function create(
        Request $request,
        DocumentManager $documentManager,
        CloudflareDnsUpdater $cloudflareDnsUpdater
    ): Response {

        $this->denyAccessUnlessGranted('ROLE_USER');

        /** @var User $user */
        $user = $this->getUser();
        $domain = new Domain();
        $form = $this->createForm(DomainType::class, $domain);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $domain->setUser($user);

            if ($cloudflareDnsUpdater->createCloudflareAccount($user)) {
                $cloudflareDnsUpdater->addDomainDNS($domain, $user);
            }

            $documentManager->persist($domain);
            $documentManager->flush();

            return $this->redirectToRoute('user_admin_domain_list');
        }

        return $this->render(
            'Admin/Domain/domain_edit.html.twig',
            [
                'form' => $form->createView(),
            ]
        );
    }

    public function edit(Request $request, Domain $domain, DocumentManager $documentManager): Response
    {
        $this->denyAccessUnlessGranted('edit', $domain);

        $form = $this->createForm(DomainType::class, $domain);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $domain->setUser($this->getUser());

            $documentManager->persist($domain);
            $documentManager->flush();

            return $this->redirectToRoute('user_admin_domain_list');
        }

        return $this->render(
            'Admin/Domain/domain_edit.html.twig',
            [
                'form' => $form->createView(),
            ]
        );
    }

    public function delete(Domain $domain, DocumentManager $documentManager, SiteRepository $siteRepository): ?Response
    {
        $this->denyAccessUnlessGranted('edit', $domain);

        $domainIsUsedBySite = $siteRepository->findOneBy(['domain' => $domain]);
        if ($domainIsUsedBySite) {
            $this->addFlash(
                'notice',
                'The domain can not be deleted as it is attached to a website!'
            );

            return $this->redirectToRoute('user_admin_domain_list');
        }

        $documentManager->remove($domain);
        $documentManager->flush();

        return $this->redirectToRoute('user_admin_domain_list');
    }
}
