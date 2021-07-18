<?php


namespace App\Controller;
use App\Entity\Shot;
use App\Entity\Site;
use App\Repository\DomainRepository;
use App\Repository\FileRepository;
use App\Repository\PageRepository;
use App\Repository\ShotRepository;
use App\Repository\SiteRepository;
use App\Service\Domain\DomainResolver;
use App\Service\Site\LayoutResolver;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ShotController extends AbstractController
{

    private $domainResolver;
    private $layoutResolver;
    private $entityManager;

    public function __construct(
        DomainResolver $domainResolver,
        LayoutResolver $layoutResolver,
        EntityManagerInterface $entityManager
    ) {
        $this->domainResolver = $domainResolver;
        $this->layoutResolver = $layoutResolver;
        $this->entityManager = $entityManager;
    }

    public function view(
        Request $request,
        SiteRepository $siteRepository,
        DomainRepository $domainRepository,
        PageRepository $pageRepository,
        ShotRepository $shotRepository,
        FileRepository $fileRepository,
        ParameterBagInterface $params,
        Shot $shot
    ): Response {

        /** @var Site $site */
        $site = $siteRepository->findOneBy(['host' => $this->domainResolver->extractDomainFromHost($request->getHost())]);

        $domain = $domainRepository->findOneBy(['name' => $this->domainResolver->extractDomainFromHost($request->getHost())]);
        if (null === $site && null !== $domain) {
            $site = $siteRepository->findOneBy(['domain' => $domain]);
        }

        if (null === $site || $shot->getSite() !== $site || $shot->getIsActive() !== true || $shot->getIsDeleted() === true) {
            throw new NotFoundHttpException();
        }

        $pages = $pageRepository->findAllActiveBySite($site);

        return $this->render(
            'UserSite/PhotographySite/minimal/shot.html.twig',
            [
                'site' => $site,
                'templateCss' => $this->layoutResolver->getSiteTemplateCss($site),
                'pages' => $pages,
                'layout' => $this->layoutResolver->getLayout($site),
            ]
        );
    }
}
