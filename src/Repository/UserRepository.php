<?php

namespace App\Repository;

use App\Document\User;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\DocumentRepository;

class UserRepository extends DocumentRepository
{
    public function __construct(DocumentManager $dm)
    {
        parent::__construct($dm, $dm->getUnitOfWork(), $dm->getClassMetadata(User::class));
    }

    public function delete(User $user)
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        return $this->dm->createQueryBuilder(User::class)
            ->remove()
            ->field('id')->equals($user->getId())
            ->getQuery()->execute();
    }
}
