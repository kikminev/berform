<?php

namespace App\Controller;

use App\Document\Message;
use App\Document\Post;
use App\Form\ContactType;
use App\Repository\PageRepository;
use App\Repository\PostRepository;
use App\Repository\SiteRepository;
use App\Service\Domain\DomainResolver;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Document\Site;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class BlogController extends AbstractController
{
    private $domainResolver;

    public function __construct(DomainResolver $domainResolver)
    {
        $this->domainResolver = $domainResolver;
    }

    public function list(
        Request $request,
        PageRepository $pageRepository,
        SiteRepository $siteRepository,
        PostRepository $postRepository
    ) {
        /** @var Site $site */
        $site = $siteRepository->findOneBy(['host' => $this->domainResolver->extractDomainFromHost($request->getHost())]);

        $posts = $postRepository->findBy(['site' => $site, 'active' => true]);
        $pages = $pageRepository->findBy(['site' => $site], ['order' => 'DESC ']);
        $page = $pageRepository->findOneBy(['site' => $site->getId(), 'slug' => !empty($slug) ? $slug : 'home']);

        $form = $this->createForm(ContactType::class, new Message(), ['action' => $this->generateUrl('user_site_contact')]);

        // todo: pagination
        return $this->render(
            'UserSite/Blog/list.html.twig',
            [
                'site' => $site,
                'pages' => $pages,
                'page' => $page,
                'slug' => 'blog',
                'posts' => $posts,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @param Request $request
     * @param $slug
     * @param PostRepository $postRepository
     * @param SiteRepository $siteRepository
     * @param PageRepository $pageRepository
     * @return Response
     */
    public function view(Request $request, $slug, PostRepository $postRepository, SiteRepository $siteRepository, PageRepository $pageRepository):Response {
        /** @var Site $site */
        $site = $siteRepository->findOneBy(['host' => $this->domainResolver->extractDomainFromHost($request->getHost())]);

        /** @var Post $post */
        $post = $postRepository->findOneBy(['site' => $site, 'slug' => $slug]);
        $page = $pageRepository->findOneBy(['site' => $site->getId(), 'slug' => !empty($slug) ? $slug : 'home']);
        $pages = $pageRepository->findBy(['site' => $site], ['order' => 'DESC ']);
        $form = $this->createForm(ContactType::class, new Message(), ['action' => $this->generateUrl('user_site_contact')]);

        if (null === $post) {
            throw new NotFoundHttpException();
        }

        return $this->render(
            'UserSite/Blog/post.html.twig',
            [
                'site' => $site,
                'post' => $post,
                'slug' => $slug,
                'page' => $page,
                'pages' => $pages,
                'files' => $post->getFiles(),
                'form' => $form->createView(),
            ]
        );
    }
}
