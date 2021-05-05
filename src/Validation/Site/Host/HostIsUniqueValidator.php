<?php

namespace App\Validation\Site\Host;

use App\Document\Site;
use App\Repository\SiteRepository;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Contracts\Translation\TranslatorInterface;

class HostIsUniqueValidator extends ConstraintValidator
{
    private TranslatorInterface $translator;
    private SiteRepository $siteRepository;

    public function __construct(SiteRepository $siteRepository, TranslatorInterface $translator)
    {
        $this->translator = $translator;
        $this->siteRepository = $siteRepository;
    }

    public function validate($value, Constraint $constraint): void
    {
        $existingSite = $this->siteRepository->findOneBy([
            'host' => $value,
        ]);

        if (null === $existingSite) {
            return;
        }

        /** @var Site $validatedPage */
        $validatedSite = $this->context->getObject();
        $validatedSiteId = $validatedSite->getId();

        if (($validatedSiteId === null && $existingSite) || ($existingSite && $existingSite->getId() !== $validatedSiteId)) {
            $this->context->buildViolation($this->translator->trans('form_site_already_used'))->addViolation();
        }
    }
}
