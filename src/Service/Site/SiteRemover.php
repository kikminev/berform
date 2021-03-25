<?php

namespace App\Service\Site;

use App\Entity\Site;
use App\Repository\AlbumRepository;
use App\Repository\FileRepository;
use App\Repository\PageRepository;
use App\Repository\SubscriptionRepository;
use App\Repository\PostRepository;
use Doctrine\ORM\EntityManagerInterface;
use RuntimeException;

class SiteRemover
{
    private PageRepository $pageRepository;
    private PostRepository $postRepository;
    private AlbumRepository $albumRepository;
    private FileRepository $fileRepository;
    private SubscriptionRepository $subscriptionRepository;
    private EntityManagerInterface $entityManager;

    public function __construct(
        PageRepository $pageRepository,
        PostRepository $postRepository,
        AlbumRepository $albumRepository,
        FileRepository $fileRepository,
        EntityManagerInterface $entityManager,
        SubscriptionRepository $subscriptionRepository
    ) {
        $this->pageRepository = $pageRepository;
        $this->postRepository = $postRepository;
        $this->albumRepository = $albumRepository;
        $this->fileRepository = $fileRepository;
        $this->subscriptionRepository = $subscriptionRepository;
        $this->entityManager = $entityManager;
    }

    public function deleteSite(Site $site): bool
    {
        if (false === strpos($site->getUserCustomer()->getEmail(), 'kik')) {
            throw new RuntimeException('Only test accounts that contain kik can be wiped out');
        }

        if ($site->getIsTemplate()) {
            throw new RuntimeException('Not possible to delete template');
        }

        $files = $this->fileRepository->findAllBySite($site);
        foreach ($files as $file) {
            $file->setIsDeleted(true);
        }

        $this->entityManager->flush();

        $this->pageRepository->deleteAllBySite($site);
        $this->albumRepository->deleteAllBySite($site);
        $this->postRepository->deleteAllBySite($site);
        $this->subscriptionRepository->deleteAllBySite($site);

        $this->entityManager->flush();

        $this->entityManager->remove($site);
        $this->entityManager->flush();

        return true;
    }
}
