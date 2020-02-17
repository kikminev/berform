<?php

namespace App\Controller;

use App\Document\Album;
use App\Document\Message;
use App\Document\Page;
use App\Document\Post;
use App\Document\Site;
use App\Form\ContactType;
use App\Repository\AlbumRepository;
use App\Repository\DomainRepository;
use App\Repository\PageRepository;
use App\Repository\PostRepository;
use App\Repository\SiteRepository;
use App\Service\Domain\DomainResolver;
use App\Service\Site\LayoutResolver;
use Doctrine\ODM\MongoDB\DocumentManager;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Pagerfanta\Adapter\DoctrineODMMongoDBAdapter;
use Pagerfanta\Pagerfanta;
use Pagerfanta\View\TwitterBootstrapView;

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

    /**
     * @param Request $request
     * @param SiteRepository $siteRepository
     * @param PageRepository $pageRepository
     * @param ParameterBagInterface $params
     * @param string $slug
     * @return Response
     */
    public function view(
        Request $request,
        SiteRepository $siteRepository,
        DomainRepository $domainRepository,
        PageRepository $pageRepository,
        AlbumRepository $albumRepository,
        ParameterBagInterface $params,
        string $slug
    ):Response {

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
        $pages = $pageRepository->findBy(['site' => $site, 'active' => true], ['order' => 'DESC ']);

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
                'files' => $album->getFiles(),
                'layout' => $this->layoutResolver->getLayout($site),
            ]
        );
    }
}
