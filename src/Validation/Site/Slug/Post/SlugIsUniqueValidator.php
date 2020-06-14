<?php

namespace App\Validation\Site\Slug\Post;

use App\Document\Post;
use App\Repository\PostRepository;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Contracts\Translation\TranslatorInterface;

class SlugIsUniqueValidator extends ConstraintValidator
{
    private PostRepository $postRepository;
    private TranslatorInterface $translator;

    public function __construct(PostRepository $postRepository, TranslatorInterface $translator)
    {
        $this->postRepository = $postRepository;
        $this->translator = $translator;
    }

    public function validate($value, Constraint $constraint): void
    {
        $existingPost = $this->postRepository->findOneBy([
            'slug' => $value,
            'site' => $this->context->getObject()->getSite(),
        ]);

        if (null === $existingPost) {
            return;
        }

        /** @var Post $validatedPost */
        $validatedPost = $this->context->getObject();

        if ($validatedPost->getId() !== $existingPost->getId()) {
            $this->context->buildViolation($this->translator->trans('form_post_already_used'))->addViolation();
        }
    }
}
