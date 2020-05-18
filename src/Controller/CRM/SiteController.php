<?php


namespace App\Controller\CRM;


use App\Document\File;
use App\Document\Site;
use App\Repository\AlbumRepository;
use App\Repository\FileRepository;
use App\Repository\PageRepository;
use App\Repository\Payment\SubscriptionRepository;
use App\Repository\PostRepository;
use App\Repository\SiteRepository;
use Doctrine\ODM\MongoDB\DocumentManager;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

class SiteController extends AbstractController
{
    private SiteRepository $siteRepository;
    private PageRepository $pageRepository;
    private PostRepository $postRepository;
    private AlbumRepository $albumRepository;
    private FileRepository $fileRepository;
    private DocumentManager $documentManager;
    private SubscriptionRepository $subscriptionRepository;

    public function __construct(
        SiteRepository $siteRepository,
        PageRepository $pageRepository,
        PostRepository $postRepository,
        AlbumRepository $albumRepository,
        FileRepository $fileRepository,
        DocumentManager $documentManager,
        SubscriptionRepository $subscriptionRepository
    ) {
        $this->siteRepository = $siteRepository;
        $this->pageRepository = $pageRepository;
        $this->postRepository = $postRepository;
        $this->albumRepository = $albumRepository;
        $this->fileRepository = $fileRepository;
        $this->documentManager = $documentManager;
        $this->subscriptionRepository = $subscriptionRepository;
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
        if ($site->isTemplate()) {
            throw new Exception('Not possible to delete template');
        }

        $files = $this->fileRepository->findAllBySite($site);
        /** @var File $file */
        foreach ($files as $file) {
            $file->setDeleted(true);
        }

        $this->documentManager->flush();

        $this->pageRepository->deleteAllBySite($site);
        $this->albumRepository->deleteAllBySite($site);
        $this->postRepository->deleteAllBySite($site);
        $this->subscriptionRepository->deleteAllBySite($site);

        $this->documentManager->remove($site);
        $this->documentManager->flush();


        $this->addFlash('admin_system_messages', 'The site has been deleted: ' . $site->getId());

        return $this->redirectToRoute('crm_list_sites');
    }
}
