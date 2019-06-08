<?php

namespace App\Controller;

use App\Document\Message;
use App\Document\Page;
use App\Document\Site;
use App\Form\ContactType;
use App\Repository\PageRepository;
use App\Repository\SiteRepository;
use App\Service\DomainResolver\DomainResolver;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class UserSiteController extends AbstractController
{
    private $domainResolver;

    public function __construct(DomainResolver $domainResolver)
    {
        $this->domainResolver = $domainResolver;
    }

    /**
     * @param Request $request
     * @param string $slug
     * @param SiteRepository $siteRepository
     * @param PageRepository $pageRepository
     * @return Response
     */
    public function renderPage(
        Request $request,
        string $slug,
        SiteRepository $siteRepository,
        PageRepository $pageRepository
    ):Response {
        /** @var Site $site */
        $site = $siteRepository->findOneBy(['host' => $this->domainResolver->extractDomainFromHost($request->getHost())]);

        /** @var Page $page */
        $page = $pageRepository->findOneBy(['site' => $site, 'slug' => !empty($slug) ? $slug : 'home']);
        $pages = $pageRepository->findBy(['site' => $site], ['order' => 'DESC ']);

        if (null === $page) {
            throw new NotFoundHttpException();
        }

        $form = $this->createForm(ContactType::class,
            new Message(),
            ['action' => $this->generateUrl('user_site_contact')]);

        return $this->render(
            'UserSite/page.html.twig',
            [
                'site' => $site,
                'files' => $page->getFiles(),
                'pages' => $pages,
                'page' => $page,
                'form' => $form->createView(),
            ]
        );
    }
}
