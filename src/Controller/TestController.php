<?php

namespace App\Controller;

use App\Document\Domain;
use App\Document\User;

//use App\Entity\UserCustomer;
use App\Entity\Page;
use App\Entity\Site;
use App\Repository\DomainRepository;
use App\Repository\PageRepository;
use App\Repository\UserCustomerRepository;
use App\Repository\SiteRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Http\Discovery\Exception\NotFoundException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\DNS\CloudflareDnsUpdater;

//use Symfony\Component\HttpClient\HttpClient;

class TestController extends AbstractController
{
    public function test(
        CloudflareDnsUpdater $cloudflareDnsUpdater,
        UserCustomerRepository $userRepository,
        DomainRepository $domainRepository,
        EntityManagerInterface $entityManager,
        SiteRepository $siteRepository,
        PageRepository $pageRepository
    ) {

        $site = $siteRepository->find(1);
        $site->setSupportedLanguages(['en']);

        $entityManager->flush();
//        $site

//        $user = $userRepository->find(1);
//        $user->setRoles(['ROLE_USER']);
//        $a = $user->getRoles();
//        print_r($a);
        exit;

        $entityManager->persist($user);
        $entityManager->flush();
        echo 'ok';
        exit;



        $site = $siteRepository->find(1);

        $page = new Page();
        $page->setSite($site);
        $page->setName('Home');
        $page->setSlug('home');
        $page->setLocale('en');
        $page->setIsActive(true);
        $page->setSequenceOrder(1);
        //        $page->setDefaultImage(1);

//        $entityManager->persist($page);
//        $entityManager->flush();



        //        $site = new Site();
        //        $site->setSlug('photography');
        //        $site->setName("Mark Williams Photography");
        //        $site->setEmail("hello@berform.com");
        //        $site->setIsTemplate(true);
        //        $site->setDefaultImage('https://berform.s3-eu-west-1.amazonaws.com/template_photography.jpg');
        //        $site->setHost('photography');
        //        $site->setCategory('photography');
        //        $site->setTemplate('minimal');
        //
        //        $entityManager->persist($site);
        //        $entityManager->flush();


        throw new NotFoundException();
    }
}
