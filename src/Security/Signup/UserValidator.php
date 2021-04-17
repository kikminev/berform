<?php

namespace App\Security\Signup;

use App\Entity\UserCustomer;
use App\Repository\UserCustomerRepository;
use Symfony\Contracts\Translation\TranslatorInterface;

class UserValidator
{
    private TranslatorInterface $translator;
    private array $errors = [];
    private UserCustomerRepository $userRepository;

    public function __construct(TranslatorInterface $translator, UserCustomerRepository $userRepository)
    {
        $this->translator = $translator;
        $this->userRepository = $userRepository;
    }

    public function validate(UserCustomer $user): bool
    {
        if($this->userRepository->findOneBy(['email' => $user->getEmail()])) {
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
