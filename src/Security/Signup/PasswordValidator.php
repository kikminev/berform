<?php

namespace App\Security\Signup;

use Symfony\Contracts\Translation\TranslatorInterface;

class PasswordValidator
{
    private TranslatorInterface $translator;
    private $errors = [];

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function validate(string $password): bool
    {
        if (strlen($password) < 5) {
            $this->errors[] = $this->translator->trans('landing_site_register_password_too_short');
        }

        if (!preg_match('#\d+#', $password)) {
            $this->errors[] = $this->translator->trans('landing_site_register_password_must_include_number');
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
