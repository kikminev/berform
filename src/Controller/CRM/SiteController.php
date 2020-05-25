<?php


namespace App\Controller\CRM;


use App\Document\Site;
use App\Repository\SiteRepository;
use App\Service\Site\SiteRemover;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

class SiteController extends AbstractController
{
    private SiteRepository $siteRepository;
    private SiteRemover $siteRemover;

    public function __construct(
        SiteRepository $siteRepository,
        SiteRemover $siteRemover
    ) {
        $this->siteRemover = $siteRemover;
        $this->siteRepository = $siteRepository;
    }

    public function list(): Response
    {
        $sites = $this->siteRepository->findAll();

        return $this->render(
            'CRM/Site/list.html.twig',
            [
                'sites' => $sites,
            ]
        );
    }

    public function delete(Site $site): RedirectResponse
    {
        if ($this->siteRemover->deleteSite($site)) {
            $this->addFlash('admin_system_messages', 'The site has been deleted: ' . $site->getId());
        } else {
            $this->addFlash('admin_system_messages', 'There was an error while deleting the site: ' . $site->getId());
        }

        return $this->redirectToRoute('crm_list_sites');
    }
}
