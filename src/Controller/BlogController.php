<?php

namespace App\Controller;

use App\Entity\File;
use App\Entity\Message;
use App\Entity\Post;
use App\Entity\Site;
use App\Form\ContactType;
use App\Repository\DomainRepository;
use App\Repository\FileRepository;
use App\Repository\PageRepository;
use App\Repository\PostRepository;
use App\Repository\SiteRepository;
use App\Service\Domain\DomainResolver;
use App\Service\Site\LayoutResolver;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class BlogController extends AbstractController
{
    private $domainResolver;
    private $layoutResolver;

    public function __construct(DomainResolver $domainResolver, LayoutResolver $layoutResolver)
    {
        $this->domainResolver = $domainResolver;
        $this->layoutResolver = $layoutResolver;
    }

    public function list(
        Request $request,
        PageRepository $pageRepository,
        SiteRepository $siteRepository,
        DomainRepository $domainRepository,
        PostRepository $postRepository
    ) {
        /** @var Site $site */
        $site = $siteRepository->findOneBy(['host' => $this->domainResolver->extractDomainFromHost($request->getHost())]);
        $domain = $domainRepository->findOneBy(['name' => $this->domainResolver->extractDomainFromHost($request->getHost())]);
        if (null === $site && null !== $domain) {
            $site = $siteRepository->findOneBy(['domain' => $domain]);
        }

        if (null === $site) {
            throw new NotFoundHttpException();
        }

        $posts = $postRepository->findActivePostsBySite($site);
        $pages = $pageRepository->findAllActiveBySite($site);
        $form = $this->createForm(ContactType::class, new Message(), ['action' => $this->generateUrl('user_site_contact')]);

        return $this->render(
            $this->layoutResolver->getBlogList($site),
            [
                'site' => $site,
                'pages' => $pages,
                'slug' => 'blog',
                'posts' => $posts,
                'templateCss' => $this->layoutResolver->getSiteTemplateCss($site),
                'form' => $form->createView(),
                'layout' => $this->layoutResolver->getLayout($site),
            ]
        );
    }

    public function view(
        Request $request,
        $slug,
        PostRepository $postRepository,
        SiteRepository $siteRepository,
        PageRepository $pageRepository,
        DomainRepository $domainRepository,
        FileRepository $fileRepository
    ): Response {

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

        /** @var Post $post */
        $post = $postRepository->findActiveBySlug($slug, $site);
        $pages = $pageRepository->findAllActiveBySite($site);
        $form = $this->createForm(ContactType::class, new Message(), ['action' => $this->generateUrl('user_site_contact')]);
        $morePosts = $postRepository->findReadMorePosts($site);

        if (null === $post) {
            throw new NotFoundHttpException();
        }

        return $this->render(
            $this->layoutResolver->getBlogPostTemplate($site),
            [
                'site' => $site,
                'post' => $post,
                'slug' => $slug,
                'pages' => $pages,
                'morePosts' => $morePosts,
                'templateCss' => $this->layoutResolver->getSiteTemplateCss($site),
                'files' => $fileRepository->findAllActiveByPost($post),
                'form' => $form->createView(),
                'layout' => $this->layoutResolver->getLayout($site),
            ]
        );
    }
}
