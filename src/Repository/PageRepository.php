<?php

namespace App\Repository;

use App\Document\Page;
use App\Document\User;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\DocumentRepository;
use Doctrine\ODM\MongoDB\MongoDBException;

class PageRepository extends DocumentRepository
{
    public function __construct(DocumentManager $dm)
    {
        parent::__construct($dm, $dm->getUnitOfWork(), $dm->getClassMetadata(Page::class));
    }

    /**
     * @param $pageIds
     * @param User $user
     * @return mixed
     * @throws MongoDBException
     */
    public function getActive($pageIds, User $user)
    {
        return $this->dm->createQueryBuilder(Page::class)
            ->field('user')->equals($user)
            ->field('deleted')->notEqual(true)
            ->field('id')->in($pageIds)
            ->getQuery()->execute();
    }
}
