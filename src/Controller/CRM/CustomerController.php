<?php


namespace App\Controller\CRM;


use App\Document\File;
use App\Document\User;
use App\Repository\FileRepository;
use App\Repository\SubscriptionRepository;
use App\Repository\UserCustomerRepository;
use App\Repository\SiteRepository;
use App\Service\Site\SiteRemover;
use RuntimeException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

class CustomerController extends AbstractController
{
    private UserCustomerRepository $userRepository;
    private SiteRepository $siteRepository;
    private SubscriptionRepository $subscriptionRepository;
    private FileRepository $fileRepository;
    private SiteRemover $siteRemover;

    public function __construct(
        UserCustomerRepository $userRepository,
        SiteRepository $siteRepository,
        FileRepository $fileRepository,
        SubscriptionRepository $subscriptionRepository,
        SiteRemover $siteRemover
    ) {
        $this->userRepository = $userRepository;
        $this->siteRepository = $siteRepository;
        $this->subscriptionRepository = $subscriptionRepository;
        $this->fileRepository = $fileRepository;
        $this->siteRemover = $siteRemover;
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
            throw new RuntimeException("Can't delete system user!");
        }

        $sites = $this->siteRepository->getByUser($user);

        $files = $this->fileRepository->findAllByUser($user);
        foreach ($files as $file) {
            /** @var File $file */
            $file->setDeleted(true);
        }

        foreach ($sites as $site) {
            $this->siteRemover->deleteSite($site);
        }

        $this->subscriptionRepository->deleteAllByUser($user);
        $this->siteRepository->deleteAllByUser($user);
        $this->userRepository->delete($user);

        $this->addFlash('admin_system_messages', 'The user has been deleted: ' . $user->getEmail());

        return $this->redirectToRoute('crm_list_customers');
    }
}
