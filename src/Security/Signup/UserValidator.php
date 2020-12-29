<?php

namespace App\Security\Signup;

use App\Document\User;
use App\Repository\UserRepositoryOld;
use Symfony\Contracts\Translation\TranslatorInterface;

class UserValidator
{
    private TranslatorInterface $translator;
    private array $errors = [];
    private UserRepositoryOld $userRepository;

    public function __construct(TranslatorInterface $translator, UserRepositoryOld $userRepository)
    {
        $this->translator = $translator;
        $this->userRepository = $userRepository;
    }

    public function validate(User $user): bool
    {
        echo $user->getEmail();
        if($this->userRepository->findOneByEmail($user->getEmail())) {
            $this->errors[] = $this->translator->trans('landing_site_register_email_used');
        }

        if (count($this->errors)) {
            return false;
        }

        return true;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}
