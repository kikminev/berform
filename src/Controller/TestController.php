<?php

namespace App\Controller;

use App\Document\Domain;
use App\Document\User;
//use App\Entity\UserCustomer;
use App\Entity\UserSite\Site;
use App\Repository\DomainRepository;
use App\Repository\UserCustomerRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Http\Discovery\Exception\NotFoundException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\DNS\CloudflareDnsUpdater;
//use Symfony\Component\HttpClient\HttpClient;

class TestController extends AbstractController
{
    public function test(CloudflareDnsUpdater $cloudflareDnsUpdater, UserCustomerRepository $userRepository, DomainRepository $domainRepository, EntityManagerInterface $entityManager)
    {


        $site = new Site();
        $site->setSlug('photography');
        $site->setName("Mark Williams Photography");
        $site->setEmail("hello@berform.com");
        $site->setIsTemplate(true);
        $site->setDefaultImage('https://berform.s3-eu-west-1.amazonaws.com/template_photography.jpg');
        $site->setHost('photography');
        $site->setCategory('photography');
        $site->setTemplate('minimal');

        $entityManager->persist($site);
        $entityManager->flush();


        throw new NotFoundException();
    }
}
