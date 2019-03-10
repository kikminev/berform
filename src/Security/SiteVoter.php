<?php

namespace App\Security;

use App\Document\Site;
use App\Document\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;

class SiteVoter extends Voter
{
    const VIEW = 'view';
    const EDIT = 'edit';

    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    protected function supports($attribute, $subject)
    {
        if (!in_array($attribute, [self::VIEW, self::EDIT])) {
            return false;
        }

        if (!$subject instanceof Site) {
            return false;
        }

        return true;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        $user = $token->getUser();

        if ($this->security->isGranted('ROLE_SUPER_ADMIN')) {
            return true;
        }

        // the user must be logged in; if not, deny access
        if (!$user instanceof User) {
            return false;
        }

        /** @var Site $subject */
        switch ($attribute) {
            case self::VIEW:
                return $this->canView($subject, $user);
            case self::EDIT:
                return $this->canEdit($subject, $user);
        }

        throw new \LogicException('Something went wrong in Site voting!');
    }

    private function canView(Site $site, User $user): bool
    {
        // if they can edit, they can view
        if ($this->canEdit($site, $user)) {
            return true;
        }

        return false;
    }

    private function canEdit(Site $site, User $user): bool
    {
        return $user === $site->getUser();
    }
}
