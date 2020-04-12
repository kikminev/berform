<?php

namespace App\Controller\Admin;

use App\Repository\DomainRepository;
use App\Repository\SiteRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class DashboardController extends AbstractController
{
    /**
     * @param SiteRepository $siteRepository
     * @param DomainRepository $domainRepository
     * @return Response
     * @throws \Doctrine\ODM\MongoDB\MongoDBException
     */
    public function dashboard(SiteRepository $siteRepository, DomainRepository $domainRepository)
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $user = $this->getUser();
        $sites = $siteRepository->getByUser($user);
        $domains = $domainRepository->findByUser($user);

        return $this->render(
            'Admin/dashboard.html.twig',
            array(
                'sites' => $sites,
                'domains' => $domains
            )
        );
    }
}
