<?php


namespace App\Validation\Site\Slug\Post;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class SlugIsUnique extends Constraint
{
    public $message = 'There is another page in this site that uses the same slug!';
}
