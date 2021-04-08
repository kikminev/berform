<?php

namespace App\Controller;

use App\Controller\Admin\UploadController;
use App\Entity\Message;
use App\Form\ContactType;
use App\Repository\AlbumRepository;
use App\Repository\DomainRepository;
//use App\Repository\PageRepository;
use App\Repository\FileRepository;
use App\Repository\PageRepository;
use App\Repository\PostRepository;
use App\Repository\SiteRepository;
use App\Service\Domain\DomainResolver;
use App\Service\Site\LayoutResolver;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Pagerfanta\Adapter\DoctrineODMMongoDBAdapter;
use Pagerfanta\Pagerfanta;
use Pagerfanta\View\TwitterBootstrapView;

class UserSiteController extends AbstractController
{
    private $domainResolver;
    private $layoutResolver;

    public function __construct(
        DomainResolver $domainResolver,
        LayoutResolver $layoutResolver
    ) {
        $this->domainResolver = $domainResolver;
        $this->layoutResolver = $layoutResolver;
    }

    public function renderPage(
        Request $request,
        SiteRepository $siteRepository,
        DomainRepository $domainRepository,
        PageRepository $pageRepository,
        AlbumRepository $albumRepository,
        PostRepository $postRepository,
        FileRepository $fileRepository,
        ParameterBagInterface $params,
        LayoutResolver $layoutResolver,
        string $slug = 'home'
    ): Response {

        // todo: fix this if and extract to a normal logic
        $site = $siteRepository->findOneBy(['host' => $this->domainResolver->extractDomainFromHost($request->getHost())]);
        $domain = $domainRepository->findOneBy(['name' => $this->domainResolver->extractDomainFromHost($request->getHost())]);
        if (null === $site && null !== $domain) {
            $site = $siteRepository->findOneBy(['domain' => $domain]);
        }

        if (null === $site) {
            throw new NotFoundHttpException();
        }

        $page = $pageRepository->findOneBy(['site' => $site->getId(), 'slug' => !empty($slug) ? $slug : 'home']);
        $pages = $pageRepository->findAllActiveBySite($site);

        if (null === $page) {
            throw new NotFoundHttpException();
        }

        $form = $this->createForm(ContactType::class, new Message(), ['action' => $this->generateUrl('user_site_contact')]);

        return $this->render(
            $this->layoutResolver->getPageTemplate($site, $slug),
            [
                'site' => $site,
                'slug' => $slug,
                'templateCss' => $this->layoutResolver->getSiteTemplateCss($site),
                'albums' => $slug === 'photography' ? $albumRepository->findAllBySite($site) : null,
                'files' => UploadController::getOrderedFiles($fileRepository->findAllActiveByPage($page)), // todo: thos should not be in controller
                'pages' => $pages,
                'page' => $page,
                'form' => $form->createView(),
                'layout' => $this->layoutResolver->getLayout($site),
                'posts' => $postRepository->findActivePostsBySite($site),
                'featuredPostInParallax' => $postRepository->findOneBy([
                    'site' => $site,
                    'isActive' => true,
                    'featuredParallax' => true,
                ]),
            ]
        );
    }


    // paginator
    //$queryBuilder = $this->documentManager->createQueryBuilder(Post::class);
    //$adapter = new DoctrineODMMongoDBAdapter($queryBuilder);
    //$pagerfanta = new Pagerfanta($adapter);
    //$pagerfanta->setMaxPerPage(5);
    //
    //$currentPageResults = $pagerfanta->getCurrentPageResults();
    //
    //
    //$routeGenerator = function($page) {
    //    return $this->generateUrl('user_site_view_page', ['slug' => 'home']) . '?page=' . $page;
    //};
    //
    //$view = new TwitterBootstrapView();
    //$options = array('proximity' => 3);
    // end paginator
}
