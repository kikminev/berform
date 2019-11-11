<?php

namespace App\Controller\Admin;

use App\Document\User;
use App\Form\Admin\ChangePasswordType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ODM\MongoDB\DocumentManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class ProfileController extends AbstractController
{
    private $documentManager;
    private $translator;

    public function __construct(DocumentManager $documentManager, TranslatorInterface $translator)
    {
        $this->documentManager = $documentManager;
        $this->translator = $translator;
    }

    public function edit(Request $request): ?Response
    {
        return $this->render(
            'Admin/Profile/edit.html.twig',
            []
        );
    }

    public function changePassword(
        Request $request,
        UserPasswordEncoderInterface $userPasswordEncoder,
        DocumentManager $documentManager
    ): ?Response {
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
                $documentManager->flush($user);
            }
        }

        return $this->render(
            'Admin/Profile/change_password.html.twig',
            ['form' => $form->createView()]
        );
    }
}
