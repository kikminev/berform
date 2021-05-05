<?php

namespace App\Controller\Admin;

use App\Form\Admin\ChangePasswordType;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class ProfileController extends AbstractController
{
    private $translator;
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager, TranslatorInterface $translator)
    {
        $this->entityManager = $entityManager;
        $this->translator = $translator;
    }

    public function edit(): ?Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        return $this->render(
            'Admin/Profile/edit.html.twig',
            []
        );
    }

    public function changePassword(
        Request $request,
        UserPasswordEncoderInterface $userPasswordEncoder
    ): ?Response {

        $this->denyAccessUnlessGranted('ROLE_USER');

        $user = $this->getUser();
        $form = $this->createForm(ChangePasswordType::class,
            $user,
            ['action' => $this->generateUrl('admin_profile_change_password')]);

        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            $oldPassword = $form['old_password']->getData();
            $newPassword = $form['new_password']->getData();
            $newPasswordRepeat = $form['new_password_repeat']->getData();

            if (!$userPasswordEncoder->isPasswordValid($user, $oldPassword)) {
                $this->addFlash('notice', $this->translator->trans('password_validation_general_not_valid'));
            }

            if ($newPassword != $newPasswordRepeat) {
                $this->addFlash('notice', $this->translator->trans('password_validation_confirm'));
            }

            if (6 > strlen($newPassword)) {
                $this->addFlash('notice', $this->translator->trans('password_validation_length_error'));
            }

            if ($userPasswordEncoder->isPasswordValid($user, $oldPassword) && $newPassword === $newPasswordRepeat) {
                $password = $userPasswordEncoder->encodePassword($user, $newPassword);
                $user->setPassword($password);
                $this->entityManager->flush($user);
            }
        }

        return $this->render(
            'Admin/Profile/change_password.html.twig',
            ['form' => $form->createView()]
        );
    }
}
