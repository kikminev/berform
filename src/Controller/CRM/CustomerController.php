<?php


namespace App\Controller\CRM;


use App\Document\File;
use App\Document\User;
use App\Repository\AlbumRepository;
use App\Repository\FileRepository;
use App\Repository\PageRepository;
use App\Repository\Payment\SubscriptionRepository;
use App\Repository\PostRepository;
use App\Repository\SiteRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

class CustomerController extends AbstractController
{
    private UserRepository $userRepository;
    private SiteRepository $siteRepository;
    private PageRepository $pageRepository;
    private AlbumRepository $albumRepository;
    private PostRepository $postRepository;
    private SubscriptionRepository $subscriptionRepository;
    private FileRepository $fileRepository;

    public function __construct(
        UserRepository $userRepository,
        SiteRepository $siteRepository,
        AlbumRepository $albumRepository,
        PostRepository $postRepository,
        PageRepository $pageRepository,
        FileRepository $fileRepository,
        SubscriptionRepository $subscriptionRepository
    ) {
        $this->userRepository = $userRepository;
        $this->siteRepository = $siteRepository;
        $this->pageRepository = $pageRepository;
        $this->albumRepository = $albumRepository;
        $this->postRepository = $postRepository;
        $this->subscriptionRepository = $subscriptionRepository;
        $this->fileRepository = $fileRepository;
    }

    public function list(): Response
    {
        $customers = $this->userRepository->findAll();

        return $this->render(
            'CRM/Customer/list.html.twig',
            [
                'customers' => $customers,
            ]
        );
    }

    public function delete(User $user): RedirectResponse
    {
        if($user->isSystem()) {
            throw new \Exception("Can't delete system user!");
        }

        $sites = $this->siteRepository->getByUser($user);

        $files = $this->fileRepository->findAllByUser($user);
        foreach ($files as $file) {
            /** @var File $file */
            $file->setDeleted(true);
        }

        foreach ($sites as $site) {
            $this->pageRepository->deleteAllBySite($site);
            $this->albumRepository->deleteAllBySite($site);
            $this->postRepository->deleteAllBySite($site);
        }

        $this->subscriptionRepository->deleteAllByUser($user);
        $this->siteRepository->deleteAllByUser($user);
        $this->userRepository->delete($user);

        $this->addFlash('admin_system_messages', 'The user has been deleted: ' . $user->getEmail());

        return $this->redirectToRoute('crm_list_customers');
    }
}
