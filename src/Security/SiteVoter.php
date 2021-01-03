<?php

namespace App\Security;

use App\Entity\Site;
use App\Entity\UserCustomer;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;

class SiteVoter extends Voter
{
    const MODIFY = 'modify';

    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    protected function supports($attribute, $subject)
    {
        if (!in_array($attribute, [self::MODIFY], false)) {
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
        if (!$user instanceof UserCustomer) {
            return false;
        }

        /** @var Site $subject */
        switch ($attribute) {
            case self::MODIFY:
            default:
                return $this->canModify($subject, $user);
        }
    }

    private function canModify(Site $site, UserCustomer $user): bool
    {
        return $user === $site->getUser();
    }
}
