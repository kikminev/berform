<?php

namespace App\Controller\Admin;

use App\Document\User;
use App\Form\Admin\ChangePasswordType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ODM\MongoDB\DocumentManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class ProfileController extends AbstractController
{
    private $documentManager;

    public function __construct(DocumentManager $documentManager)
    {
        $this->documentManager = $documentManager;
    }

    public function edit(Request $request): ?Response
    {
        return $this->render(
            'Admin/profile/edit.html.twig',
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
                $this->addFlash('notice', 'Your password is not valid!');
            }

            if ($newPassword != $newPasswordRepeat) {
                $this->addFlash('notice', 'Please confirm your new password!');
            }

            if (6 > strlen($newPassword)) {
                $this->addFlash('notice', 'Your new password should at least 6 characters long.');
            }

            if ($userPasswordEncoder->isPasswordValid($user, $oldPassword) && $newPassword === $newPasswordRepeat) {
                $password = $userPasswordEncoder->encodePassword($user, $newPassword);
                $user->setPassword($password);
                $documentManager->flush($user);
            }
        }

        return $this->render(
            'Admin/profile/change_password.html.twig',
            ['form' => $form->createView()]
        );
    }
}
