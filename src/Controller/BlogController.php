<?php

namespace App\Controller;

use App\Document\Message;
use App\Document\Page;
use App\Form\ContactType;
use App\Repository\PageRepository;
use App\Repository\PostRepository;
use App\Service\Domain\DomainResolver;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ODM\MongoDB\DocumentManager;
use App\Document\Site;
use Mailgun\Mailgun;

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
        PostRepository $postRepository
    ) {
        /** @var Site $site */
        $site = $postRepository->findOneBy(['host' => $this->domainResolver->extractDomainFromHost($request->getHost())]);

        $posts = $postRepository->findBy(['site' => $site]);
        $pages = $pageRepository->findBy(['site' => $site], ['order' => 'DESC ']);

        $form = $this->createForm(ContactType::class, new Message(), ['action' => $this->generateUrl('user_site_contact')]);

        return $this->render(
            'UserSite/page.html.twig',
            [
                'site' => $site,
                'pages' => $pages,
                'posts' => $posts,
                'form' => $form->createView(),
            ]
        );
    }
}
