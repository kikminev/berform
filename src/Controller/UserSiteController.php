<?php

namespace App\Controller;

use App\Document\Message;
use App\Form\ContactType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ODM\MongoDB\DocumentManager;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use App\Document\Site;
use App\Document\Page;
use App\Document\File;

class UserSiteController extends AbstractController
{
    public function renderPage(Request $request, string $slug, DocumentManager $documentManager)
    {
        // todo: when creating the site choose domain or subdomain
        $site = $documentManager->getRepository(Site::class)->findOneBy(['host' => $request->getHost()]);
        $page = $documentManager->getRepository(Page::class)->findOneBy(['site' => $site, 'slug' => $slug]);
        $pages = $documentManager->getRepository(Page::class)->findBy(array('site' => $site), array('order' => 'DESC '));
        $files = $documentManager->getRepository(File::class)->findBy(['page' => $page], ['order' => 'DESC ']);

        if(null === $page) {
            throw new NotFoundHttpException();
        }

        $form = $this->createForm(ContactType::class, new Message(), ['action' => $this->generateUrl('user_site_contact')]);

        return $this->render(
            'UserSite/page.html.twig',
            array(
                'files' => $files,
                'pages' => $pages,
                'page' => $page,
                'form' => $form->createView(),
            )
        );
    }
}
