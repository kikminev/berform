<?php

namespace App\Repository;

use App\Document\Site;
use App\Document\User;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\DocumentRepository;

class SiteRepository extends DocumentRepository
{
    public function __construct(DocumentManager $dm)
    {
        parent::__construct($dm, $dm->getUnitOfWork(), $dm->getClassMetadata(Site::class));
    }

    /**
     * @param User $user
     * @return mixed
     * @throws \Doctrine\ODM\MongoDB\MongoDBException
     */
    public function getByUser(User $user)
    {
        $qb = $this->createQueryBuilder();
        $qb->addAnd($qb->expr()->field('user')->equals($user));
        $qb->addAnd($qb->expr()->field('deleted')->notEqual(true));
        $qb->addAnd($qb->expr()->field('archived')->notEqual(true));

        return $qb->getQuery()->execute();
    }

    public function getTemplates(): array
    {
        return $this->findBy(['isTemplate' => true]);
    }
}
