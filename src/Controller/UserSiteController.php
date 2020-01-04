<?php

namespace App\Controller;

use App\Document\Message;
use App\Document\Page;
use App\Document\Post;
use App\Document\Site;
use App\Form\ContactType;
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

class UserSiteController extends AbstractController
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
    public function renderPage(
        Request $request,
        SiteRepository $siteRepository,
        DomainRepository $domainRepository,
        PageRepository $pageRepository,
        PostRepository $postRepository,
        ParameterBagInterface $params,
        LayoutResolver $layoutResolver,
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


        $pageLayout = $this->layoutResolver->isBlogTemplate($site->getTemplate()) ? 'UserSite/Blog/blog_home_page.html.twig' : 'UserSite/page.html.twig';


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

        return $this->render(
            $pageLayout,
            [
                'site' => $site,
                'slug' => $slug,
                'files' => $page->getFiles(),
                'pages' => $pages,
                'page' => $page,
                'form' => $form->createView(),
                'layout' => $this->layoutResolver->getLayout($site->getTemplate()),
                // blog
                'isBlogTemplate' => $this->layoutResolver->isBlogTemplate($site->getTemplate()) ,
                'posts' => $this->layoutResolver->isBlogTemplate($site->getTemplate()) ?  $postRepository->findActivePosts($site) : null,
                'featuredPostInParallax' => $postRepository->findOneBy(['site' => $site, 'active' => true, 'featuredParallax' => true]),
                //'paginator' => $view->render($pagerfanta, $routeGenerator, $options),
            ]
        );
    }
}