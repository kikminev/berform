<?php

namespace App\Controller;

use App\Document\Page;
use App\Document\Site;
use App\Repository\SiteRepository;
use Doctrine\ODM\MongoDB\DocumentManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

use App\Form\UserType;
use App\Document\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

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
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function register(
        Request $request,
        UserPasswordEncoderInterface $passwordEncoder,
        DocumentManager $documentManager
    ) {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
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
            'signup/register.html.twig',
            ['form' => $form->createView()]
        );
    }


    public function chooseTemplate(Site $site, SessionInterface $session)
    {
        $session->set('selectedTemplate', $site->getId());

        return $this->json(['message' => 'ok']);
    }


    /**
     * @param SessionInterface $session
     * @param SiteRepository $siteRepository
     * @param DocumentManager $documentManager
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \Doctrine\ODM\MongoDB\LockException
     * @throws \Doctrine\ODM\MongoDB\Mapping\MappingException
     */
    public function setupAccount(SessionInterface $session, SiteRepository $siteRepository, DocumentManager $documentManager)
    {
        if ($selectedTemplate = $session->get('selectedTemplate')) {

            /** @var Site $selectedTemplate */
            /** @var Site newte */
            $selectedTemplate = $siteRepository->find($selectedTemplate);
            $newSite = clone $selectedTemplate;

            $newSite->setUser($this->getUser());
            $documentManager->persist($newSite);

            $pages = $newSite->getPages();
            foreach ($pages as $page) {
                /** @var Page $newPage */
                $newPage = clone $page;
                $newPage->setUser($this->getUser());
                $newPage->setSite($newSite);

                $documentManager->persist($newPage);
            }

            $documentManager->flush();

            $session->remove('selectedTemplate');
        }

        return $this->redirectToRoute('admin');
    }
}
