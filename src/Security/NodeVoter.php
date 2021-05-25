<?php


namespace App\Security;


use App\Entity\Node;
use App\Entity\UserCustomer;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Security;

class NodeVoter extends Voter
{
    const MODIFY = 'modify';

    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    protected function supports($attribute, $subject)
    {
        if ($attribute !== self::MODIFY) {
            return false;
        }

        if (!$subject instanceof Node) {
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

        if(null === $user) {
            return false;
        }

        if (!$user instanceof UserCustomer) {
            return false;
        }

        /** @var Node $subject */
        switch ($attribute) {
            case self::MODIFY:
            default:
                return $this->canModify($subject, $user);
        }
    }

    private function canModify(Node $node, UserCustomer $user): bool
    {
        if($node->getIsDeleted()) {
            return false;
        }

        return $user === $node->getUserCustomer();
    }
}
