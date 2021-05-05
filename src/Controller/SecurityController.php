<?php

namespace App\Controller;

use App\Entity\Album;
use App\Entity\Product;
use App\Entity\Subscription;
use App\Entity\Page;
use App\Entity\Post;
use App\Entity\Site;
use App\Entity\UserCustomer;
use App\Repository\AlbumRepository;
use App\Repository\PostRepository;
use App\Repository\ProductRepository;
use App\Repository\SiteRepository;
use App\Security\Signup\PasswordValidator;
use App\Security\Signup\UserValidator;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use \Symfony\Component\HttpFoundation\RedirectResponse;
use \Symfony\Component\HttpFoundation\JsonResponse;
use App\Form\UserType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class SignupController
 * @package App\Controller
 */
class SecurityController extends AbstractController
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

        return $this->render('Signup/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * @param Request $request
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param UserValidator $userValidator
     * @param PasswordValidator $passwordValidator
     * @param TranslatorInterface $translator
     * @param EntityManagerInterface $entityManager
     * @return RedirectResponse|Response
     */
    public function register(
        Request $request,
        UserPasswordEncoderInterface $passwordEncoder,
        UserValidator $userValidator,
        PasswordValidator $passwordValidator,
        TranslatorInterface $translator,
        EntityManagerInterface $entityManager
    ) {
        if (null !== $this->getUser()) {
            return $this->redirectToRoute('app_signup_setup_account');
        }
        
        $user = new UserCustomer();
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
            $user->setIsActive(true);
            $user->setIsSystem(false);
            $user->setCreatedAt(new DateTime());
            $user->setUpdatedAt(new DateTime());

            $entityManager->persist($user);
            $entityManager->flush();

            $token = new UsernamePasswordToken($user, null, 'main', $user->getRoles());
            $this->container->get('security.token_storage')->setToken($token);
            $this->container->get('session')->set('_security_main', serialize($token));

            return $this->redirectToRoute('app_signup_setup_account');
        }

        return $this->render(
            'Signup/registration.html.twig',
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
        EntityManagerInterface $entityManager
    ): RedirectResponse {
        // todo: this needs to be implemented
        if ($selectedTemplate = $session->get('selectedTemplate')) {

            if(null == $this->getUser()) {
                return $this->redirectToRoute('app_registration');
            }

            /** @noinspection PhpUnhandledExceptionInspection */
            /** @var Site $selectedTemplate */
            $selectedTemplate = $siteRepository->find($selectedTemplate);
            if (null === $selectedTemplate) {
                return $this->redirectToRoute('index');
            }

            $newSite = clone $selectedTemplate;

            // todo: this needs a service method
            /** @var UserCustomer $user */
            $user = $this->getUser();
            $userEmail = $user->getUsername();
            $emailName = explode('@', $userEmail);
            /** @noinspection PhpUnhandledExceptionInspection */
            $host = $emailName[0] . random_int(1, 10000000) . time();

            $newSite->setHost($host);
            $newSite->setUserCustomer($user);
            $newSite->setIsTemplate(false);
            $entityManager->persist($newSite);

            $pages = $selectedTemplate->getPages();

            foreach ($pages as $page) {
                /** @var Page $newPage */
                $newPage = clone $page;
                $newPage->setUserCustomer($user);
                $newPage->setSite($newSite);

                $entityManager->persist($newPage);
            }

            $albums = $albumRepository->findAllByUserSite($selectedTemplate->getUserCustomer(), $selectedTemplate);
            /** @var Album $album */
            foreach ($albums as $album) {
                /** @var Page $newPage */
                $newAlbum = clone $album;
                $newAlbum->setUserCustomer($user);
                $newAlbum->setSite($newSite);

                $entityManager->persist($newAlbum);
            }

            $posts = $postRepository->findAllByUserSite($selectedTemplate->getUserCustomer(), $selectedTemplate);
            foreach ($posts as $post) {
                /** @var Post $newPost */
                $newPost = clone $post;
                $newPost->setUserCustomer($user);
                $newPost->setSite($newSite);

                $entityManager->persist($newPost);
            }

            $subscription = new Subscription();
            $subscription->setProduct($productRepository->findOneBySystemCode(Product::PRODUCT_TYPE_FREE_HOSTING));
            $subscription->setUserCustomer($user);
            $subscription->setSite($newSite);
            $subscription->setCreatedAt(new DateTime());
            $subscription->setUpdatedAt(new DateTime());
            $subscription->setExpiresAt(new DateTime('+ 7 days'));
            $subscription->setCreatedAt(new DateTime());
            $subscription->setUpdatedAt(new DateTime());

            $entityManager->persist($subscription);

            $entityManager->flush();

            $session->remove('selectedTemplate');
        }

        return $this->redirectToRoute('admin');
    }

    public function previewSiteBeforeCreation(SessionInterface $session, SiteRepository $siteRepository) {

        $selectedTemplate = $session->get('selectedTemplate');
        if(null === $selectedTemplate) {
            return $this->redirectToRoute('index');
        }

        $selectedTemplate = $siteRepository->find($selectedTemplate);
        if (null === $selectedTemplate) {
            return $this->redirectToRoute('index');
        }

        return $this->render(
            'Signup/template_preview.html.twig',
            [
                'template' => $selectedTemplate
            ]
        );

    }
}
