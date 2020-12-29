<?php

namespace App\Controller;

use App\Document\Domain;
use App\Document\User;
use App\Repository\DomainRepository;
use App\Repository\UserRepositoryOld;
use Http\Discovery\Exception\NotFoundException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\DNS\CloudflareDnsUpdater;
//use Symfony\Component\HttpClient\HttpClient;

class TestController extends AbstractController
{
    public function test(CloudflareDnsUpdater $cloudflareDnsUpdater, UserRepositoryOld $userRepository, DomainRepository $domainRepository)
    {
        throw new NotFoundException();
        /** @var User $user */
        $user = $userRepository->find('5ea4506603ecf2226a1cd452');

        /** @var Domain $domain */
        $domain = $domainRepository->find('5eb282e0312ceb10593d0432');

        if ($cloudflareDnsUpdater->createCloudflareAccount($user)) {
            $cloudflareDnsUpdater->addDomainDNS($domain, $user);
        }
        exit;
    }
}
