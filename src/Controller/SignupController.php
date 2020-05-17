<?php

namespace App\Controller;

use App\Document\Page;
use App\Document\Payment\Product;
use App\Document\Site;
use App\Document\Payment\Subscription;
use App\Repository\AlbumRepository;
use App\Repository\Payment\ProductRepository;
use App\Repository\PostRepository;
use App\Repository\SiteRepository;
use App\Repository\UserRepository;
use App\Security\Signup\PasswordValidator;
use App\Security\Signup\UserValidator;
use DateTime;
use Doctrine\ODM\MongoDB\DocumentManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use \Symfony\Component\HttpFoundation\RedirectResponse;
use \Symfony\Component\HttpFoundation\JsonResponse;
use App\Form\UserType;
use App\Document\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class SignupController
 * @package App\Controller
 */
class SignupController extends AbstractController
{
    /**
     * @param AuthenticationUtils $authenticationUtils
     * @return Response
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('signup/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * @param Request $request
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param DocumentManager $documentManager
     * @return RedirectResponse|Response
     */
    public function register(
        Request $request,
        UserPasswordEncoderInterface $passwordEncoder,
        UserValidator $userValidator,
        PasswordValidator $passwordValidator,
        TranslatorInterface $translator,
        DocumentManager $documentManager
    ) {
        if (null !== $this->getUser()) {
            return $this->redirectToRoute('app_signup_setup_account');
        }


        $user = new User();
        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if (!$userValidator->validate($form->getData())) {
                foreach ($userValidator->getErrors() as $error) {
                    $this->addFlash('registration_messages', $error);
                }

                return $this->redirectToRoute('app_registration');
            }

            if (!$passwordValidator->validate($user->getPlainPassword())) {
                foreach ($passwordValidator->getErrors() as $error) {
                    $this->addFlash('registration_messages', $error);
                }

                return $this->redirectToRoute('app_registration');
            }

            $password = $passwordEncoder->encodePassword($user, $user->getPlainPassword());
            $user->setPassword($password);

            $documentManager->persist($user);
            $documentManager->flush();

            $token = new UsernamePasswordToken($user, null, 'main', $user->getRoles());
            $this->container->get('security.token_storage')->setToken($token);
            $this->container->get('session')->set('_security_main', serialize($token));

            return $this->redirectToRoute('app_signup_setup_account');
        }

        return $this->render(
            'signup/registration.html.twig',
            ['form' => $form->createView()]
        );
    }

    public function chooseTemplate(Site $site, SessionInterface $session): JsonResponse
    {
        $session->set('selectedTemplate', $site->getId());

        return $this->json(['message' => 'ok']);
    }

    public function setupAccount(
        SessionInterface $session,
        SiteRepository $siteRepository,
        ProductRepository $productRepository,
        AlbumRepository $albumRepository,
        PostRepository $postRepository,
        DocumentManager $documentManager
    ): RedirectResponse {
        if ($selectedTemplate = $session->get('selectedTemplate')) {

            /** @noinspection PhpUnhandledExceptionInspection */
            /** @var Site $selectedTemplate */
            $selectedTemplate = $siteRepository->find($selectedTemplate);
            if (null === $selectedTemplate) {
                return $this->redirectToRoute('home');
            }

            $newSite = clone $selectedTemplate;

            // todo: this needs a service method
            /** @var User $user */
            $user = $this->getUser();
            $userEmail = $user->getUsername();
            $emailName = explode('@', $userEmail);
            /** @noinspection PhpUnhandledExceptionInspection */
            $host = $emailName[0] . random_int(1, 10000000) . time();

            $newSite->setHost($host);
            $newSite->setUser($user);
            $newSite->setIsTemplate(false);
            $documentManager->persist($newSite);

            $pages = $selectedTemplate->getPages();

            foreach ($pages as $page) {
                /** @var Page $newPage */
                $newPage = clone $page;
                $newPage->setUser($user);
                $newPage->setSite($newSite);

                $documentManager->persist($newPage);
            }

            $albums = $albumRepository->findAllByUserSite($selectedTemplate->getUser(), $selectedTemplate);
            foreach ($albums as $album) {
                /** @var Page $newPage */
                $newAlbum = clone $album;
                $newAlbum->setUser($user);
                $newAlbum->setSite($newSite);

                $documentManager->persist($newAlbum);
            }

            $posts = $postRepository->findAllByUserSite($selectedTemplate->getUser(), $selectedTemplate);
            foreach ($posts as $post) {
                /** @var Page $newPage */
                $newPost = clone $post;
                $newPost->setUser($user);
                $newPost->setSite($newSite);

                $documentManager->persist($newPost);
            }

            $subscription = new Subscription();
            $subscription->setProduct($productRepository->findOneBySystemCode(Product::PRODUCT_TYPE_FREE_HOSTING));
            $subscription->setUser($user);
            $subscription->setCreatedAt(new DateTime());
            $subscription->setUpdatedAt(new DateTime());
            $documentManager->persist($subscription);

            $documentManager->flush();

            $session->remove('selectedTemplate');
        }

        return $this->redirectToRoute('admin');
    }
}
