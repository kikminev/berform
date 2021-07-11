<?php

namespace App\Controller;

use App\Controller\Admin\UploadController;
use App\Entity\Message;
use App\Entity\Post;
use App\Form\ContactType;
use App\Repository\AlbumRepository;
use App\Repository\DomainRepository;
use App\Repository\FileRepository;
use App\Repository\PageRepository;
use App\Repository\PostRepository;
use App\Repository\SiteRepository;
use App\Service\Domain\DomainResolver;
use App\Service\Site\LayoutResolver;
use App\Service\Site\TemplatePiecesProvider;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class UserSiteController extends AbstractController
{
    private $domainResolver;
    private $layoutResolver;
    private TemplatePiecesProvider $templatePiecesProvider;

    public function __construct(
        DomainResolver $domainResolver,
        LayoutResolver $layoutResolver,
        TemplatePiecesProvider $templatePiecesProvider
    ) {
        $this->domainResolver = $domainResolver;
        $this->layoutResolver = $layoutResolver;
        $this->templatePiecesProvider = $templatePiecesProvider;
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
        if (null === $site && null !== $domain && null !== $domain->getSite()) {
            $site = $domain->getSite();
        }

        if (null === $site) {
            throw new NotFoundHttpException();
        }

        $page = $pageRepository->findOneBy(['site' => $site->getId(), 'slug' => !empty($slug) ? $slug : 'home', 'isActive' => true]);
        $pages = $pageRepository->findAllActiveBySite($site);

        if (null === $page) {
            throw new NotFoundHttpException();
        }

        $form = $this->createForm(ContactType::class, new Message(), ['action' => $this->generateUrl('user_site_contact')]);
        $templatePieces = $this->templatePiecesProvider->getPieces($site, $slug);

        return $this->render(
            $this->layoutResolver->getPageTemplate($site, $slug),
            array_merge([
                'site' => $site,
                'slug' => $slug,
                'templateCss' => $this->layoutResolver->getSiteTemplateCss($site),
                'albums' => $slug === 'photography' ? $albumRepository->findAllBySite($site) : null,
                'files' => UploadController::getOrderedFiles($fileRepository->findAllActiveByPage($page)), // todo: thos should not be in controller
                'pages' => $pages,
                'page' => $page,
                'form' => $form->createView(),
                'layout' => $this->layoutResolver->getLayout($site),
            ], $templatePieces)
        );
    }
}
