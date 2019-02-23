<?php

namespace App\Repository;

use App\Document\Domain;
use App\Document\User;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\DocumentRepository;

class DomainRepository extends DocumentRepository
{
    public function __construct(DocumentManager $dm)
    {
        parent::__construct($dm, $dm->getUnitOfWork(), $dm->getClassMetadata(Domain::class));
    }

    /**
     * @param User $user
     * @throws \Doctrine\ODM\MongoDB\MongoDBException
     */
    public function getByUser(User $user)
    {
        $qb = $this->createQueryBuilder();
        $qb->addAnd($qb->expr()->field('user')->equals($user));
        $qb->addAnd($qb->expr()->field('deleted')->notEqual(true));

        return $qb->getQuery()->execute();
    }
}
