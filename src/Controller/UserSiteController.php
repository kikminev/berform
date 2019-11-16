<?php

namespace App\Controller;

use App\Document\Message;
use App\Document\Page;
use App\Document\Site;
use App\Form\ContactType;
use App\Repository\DomainRepository;
use App\Repository\PageRepository;
use App\Repository\SiteRepository;
use App\Service\Domain\DomainResolver;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
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
     * @param SiteRepository $siteRepository
     * @param PageRepository $pageRepository
     * @param ParameterBagInterface $params
     * @param string $slug
     * @return Response
     */
    public function renderPage(
        Request $request,
        SiteRepository $siteRepository,
        DomainRepository $domainRepository,
        PageRepository $pageRepository,
        ParameterBagInterface $params,
        string $slug = 'home'
    ):Response {

        // todo: fix this if and extract to a normal logic
        /** @var Site $site */
        $site = $siteRepository->findOneBy(['host' => $this->domainResolver->extractDomainFromHost($request->getHost())]);

        $domain = $domainRepository->findOneBy(['name' => $this->domainResolver->extractDomainFromHost($request->getHost())]);
        if (null === $site && null !== $domain) {
            $site = $siteRepository->findOneBy(['domain' => $domain]);
        }

        if (null === $site) {
            throw new NotFoundHttpException();
        }

        /** @var Page $page */
        $page = $pageRepository->findOneBy(['site' => $site->getId(), 'slug' => !empty($slug) ? $slug : 'home']);
        $pages = $pageRepository->findBy(['site' => $site, 'active' => true], ['order' => 'DESC ']);

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
                'slug' => $slug,
                'files' => $page->getFiles(),
                'pages' => $pages,
                'page' => $page,
                'form' => $form->createView(),
            ]
        );
    }
}
