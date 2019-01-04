<?php

namespace App\Controller;

use App\Document\ContactMessage;
use App\Form\ContactType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ODM\MongoDB\DocumentManager;
use App\Document\Site;
use App\Document\Page;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class UserSiteController extends AbstractController
{
    public function renderPage(Request $request, string $pageSlug, DocumentManager $documentManager)
    {
        // todo: when creating the site choose domain or subdomain
        $site = $documentManager->getRepository(Site::class)->findOneBy(['host' => $request->getHost()]);
        $page = $documentManager->getRepository(Page::class)->findOneBy(['site' => $site, 'slug' => $pageSlug]);

        if(null === $page) {
            throw new NotFoundHttpException();
        }

        $form = $this->createForm(ContactType::class, new ContactMessage());

        return $this->render(
            'UserSite/page.html.twig',
            array(
                'page' => $page,
                'form' => $form->createView(),
            )
        );
    }
}
