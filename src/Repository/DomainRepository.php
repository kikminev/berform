<?php

namespace App\Repository;

use App\Document\Domain;
use App\Entity\UserCustomer;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\DocumentRepository;

class DomainRepository extends DocumentRepository
{
    public function __construct(DocumentManager $dm)
    {
        parent::__construct($dm, $dm->getUnitOfWork(), $dm->getClassMetadata(Domain::class));
    }

    public function findByUser(UserCustomer $user)
    {
        $qb = $this->createQueryBuilder();
        $qb->addAnd($qb->expr()->field('user')->equals($user));
        $qb->addAnd($qb->expr()->field('deleted')->notEqual(true));

        /** @noinspection PhpUnhandledExceptionInspection */
        return $qb->getQuery()->execute();
    }

    public function findActiveByUser(UserCustomer $user)
    {
        $qb = $this->createQueryBuilder();
        $qb->addAnd($qb->expr()->field('user')->equals($user));
        $qb->addAnd($qb->expr()->field('deleted')->notEqual(true));
        $qb->addAnd($qb->expr()->field('active')->equals(true));

        /** @noinspection PhpUnhandledExceptionInspection */
        return $qb->getQuery()->execute();
    }
}
