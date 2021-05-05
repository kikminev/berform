<?php


namespace App\Validation\Site\Host;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class HostIsUnique extends Constraint
{
    public $message = 'Thos site host is already taken!';
}
