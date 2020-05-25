<?php

namespace App\Service\Site;

use App\Document\File;
use App\Document\Site;
use App\Repository\AlbumRepository;
use App\Repository\FileRepository;
use App\Repository\PageRepository;
use App\Repository\Payment\SubscriptionRepository;
use App\Repository\PostRepository;
use Doctrine\ODM\MongoDB\DocumentManager;
use RuntimeException;

class SiteRemover
{
    private PageRepository $pageRepository;
    private PostRepository $postRepository;
    private AlbumRepository $albumRepository;
    private FileRepository $fileRepository;
    private DocumentManager $documentManager;
    private SubscriptionRepository $subscriptionRepository;

    public function __construct(
        PageRepository $pageRepository,
        PostRepository $postRepository,
        AlbumRepository $albumRepository,
        FileRepository $fileRepository,
        DocumentManager $documentManager,
        SubscriptionRepository $subscriptionRepository
    ) {
        $this->pageRepository = $pageRepository;
        $this->postRepository = $postRepository;
        $this->albumRepository = $albumRepository;
        $this->fileRepository = $fileRepository;
        $this->documentManager = $documentManager;
        $this->subscriptionRepository = $subscriptionRepository;
    }

    public function deleteSite(Site $site): bool
    {
        if ($site->isTemplate()) {
            throw new RuntimeException('Not possible to delete template');
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

        return true;
    }
}
