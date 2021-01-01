<?php

namespace App\Validation\Site\Slug\Page;

use App\Document\Page;
//use App\Repository\PageRepository;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Contracts\Translation\TranslatorInterface;

class SlugIsUniqueValidator extends ConstraintValidator
{
//    private PageRepository $pageRepository;
    private TranslatorInterface $translator;

//    public function __construct(PageRepository $pageRepository, TranslatorInterface $translator)
//    {
//        $this->pageRepository = $pageRepository;
//        $this->translator = $translator;
//    }

    public function validate($value, Constraint $constraint): void
    {
        $existingPage = $this->pageRepository->findOneBy([
            'slug' => $value,
            'site' => $this->context->getObject()->getSite(),
        ]);
        if (null === $existingPage) {
            return;
        }

        /** @var Page $validatedPage */
        $validatedPage = $this->context->getObject();
        $validatedPageId = $validatedPage->getId();

        if (($validatedPageId === null && $existingPage) || ($existingPage && $existingPage->getId() !== $validatedPageId)) {
            $this->context->buildViolation($this->translator->trans('form_page_already_used'))->addViolation();
        }
    }
}
