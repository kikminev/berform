<?php

namespace App\Controller;

use App\Document\Message;
use App\Form\ContactType;
use App\Repository\FileRepository;
use App\Repository\PageRepository;
use App\Repository\SiteRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class UserSiteController extends AbstractController
{
    /**
     * @param Request $request
     * @param string $slug
     * @param SiteRepository $siteRepository
     * @param PageRepository $pageRepository
     * @param FileRepository $fileRepository
     * @return Response
     * @throws \Doctrine\ODM\MongoDB\MongoDBException
     */
    public function renderPage(
        Request $request,
        string $slug,
        SiteRepository $siteRepository,
        PageRepository $pageRepository,
        FileRepository $fileRepository
    ) {
        // todo: when creating the site choose domain or subdomain
        $site = $siteRepository->findOneBy(['host' => $request->getHost()]);
        $page = $pageRepository->findOneBy(['site' => $site, 'slug' => $slug]);
        $pages = $pageRepository->findBy(['site' => $site], ['order' => 'DESC ']);
        $files = $fileRepository->getPageFiles($page);

        if (null === $page) {
            throw new NotFoundHttpException();
        }

        $form = $this->createForm(ContactType::class,
            new Message(),
            ['action' => $this->generateUrl('user_site_contact')]);

        return $this->render(
            'UserSite/page.html.twig',
            [
                'files' => $files,
                'pages' => $pages,
                'page' => $page,
                'form' => $form->createView(),
            ]
        );
    }
}
