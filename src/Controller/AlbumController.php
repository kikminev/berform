<?php

namespace App\Controller;

use App\Controller\Admin\UploadController;
use App\Document\Album;
use App\Document\Site;
use App\Repository\AlbumRepository;
use App\Repository\DomainRepository;
use App\Repository\PageRepository;
use App\Repository\SiteRepository;
use App\Service\Domain\DomainResolver;
use App\Service\Site\LayoutResolver;
use Doctrine\ODM\MongoDB\DocumentManager;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class AlbumController extends AbstractController
{
    private $domainResolver;
    private $layoutResolver;
    private $documentManager;

    public function __construct(DomainResolver $domainResolver, LayoutResolver $layoutResolver, DocumentManager $documentManager)
    {
        $this->domainResolver = $domainResolver;
        $this->layoutResolver = $layoutResolver;
        $this->documentManager = $documentManager;
    }

    public function view(
        Request $request,
        SiteRepository $siteRepository,
        DomainRepository $domainRepository,
        PageRepository $pageRepository,
        AlbumRepository $albumRepository,
        ParameterBagInterface $params,
        string $slug
    ):Response {

        /** @var Site $site */
        $site = $siteRepository->findOneBy(['host' => $this->domainResolver->extractDomainFromHost($request->getHost())]);

        $domain = $domainRepository->findOneBy(['name' => $this->domainResolver->extractDomainFromHost($request->getHost())]);
        if (null === $site && null !== $domain) {
            $site = $siteRepository->findOneBy(['domain' => $domain]);
        }

        if (null === $site) {
            throw new NotFoundHttpException();
        }

        /** @var Album $album */
        $album = $albumRepository->findOneBy(['active' => true, 'slug' => $slug]);
        $pages = $pageRepository->findBy(['site' => $site, 'active' => true], ['order' => 'ASC']);

        if (null === $album) {
            throw new NotFoundHttpException();
        }

        return $this->render(
            'UserSite/PhotographySite/minimal/album.html.twig',
            [
                'site' => $site,
                'slug' => $slug,
                'templateCss' => $this->layoutResolver->getSiteTemplateCss($site),
                'pages' => $pages,
                'album' => $album,
                'files' => UploadController::getOrderedFiles($album->getFiles()->toArray()),
                'layout' => $this->layoutResolver->getLayout($site),
            ]
        );
    }
}
