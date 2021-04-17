<?php

namespace App\Controller\Admin;

use App\DNS\CloudflareDnsUpdater;
use App\Entity\Domain;
use App\Entity\UserCustomer;
use App\Form\Admin\DomainType;
use App\Repository\DomainRepository;
use App\Repository\SiteRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ODM\MongoDB\DocumentManager;
use Symfony\Contracts\Translation\TranslatorInterface;

class DomainController extends AbstractController
{
    private TranslatorInterface $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * @param DomainRepository $domainRepository
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Doctrine\ODM\MongoDB\MongoDBException
     */
    public function list(DomainRepository $domainRepository)
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $domains = $domainRepository->findByUserCustomer($this->getUser());

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
        DomainRepository $domainRepository,
        CloudflareDnsUpdater $cloudflareDnsUpdater
    ): Response {

        $this->denyAccessUnlessGranted('ROLE_USER');

        /** @var UserCustomer $user */
        $user = $this->getUser();
        $domain = new Domain();
        $form = $this->createForm(DomainType::class, $domain);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            if(null !== $domainRepository->findOneBy(['name' => $domain->getName()])) {
                $this->addFlash('domain_list_notice', $this->translator->trans('admin_create_domain_error_already_exists'));

                return $this->redirectToRoute('user_admin_domain_list');
            }

            $domain->setUserCustomer($user);

            if ($cloudflareDnsUpdater->createCloudflareAccount($user)) {
                if(true !== $cloudflareDnsUpdater->addDomainDNS($domain, $user)) {
                    $this->addFlash('domain_list_notice', $this->translator->trans('admin_create_domain_error_unknown'));
                }
            }

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
                'domain' => $domain,
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
