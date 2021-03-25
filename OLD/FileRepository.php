<?php

namespace App\OLD\Repository;

use App\Document\File;
use App\Document\Page;
use App\Document\Post;
use App\Document\Site;
use App\Document\User;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\DocumentRepository;

// todo: move this to be universal for page and post
class FileRepository extends DocumentRepository
{
    public function __construct(DocumentManager $dm)
    {
        parent::__construct($dm, $dm->getUnitOfWork(), $dm->getClassMetadata(File::class));
    }


    /**
     * @param $fileId
     * @return mixed
     * @throws \Doctrine\ODM\MongoDB\MongoDBException
     */
    public function getActiveByIds($fileIds, User $user)
    {
        return $this->dm->createQueryBuilder(File::class)
            ->field('user')->equals($user)
            ->field('deleted')->notEqual(true)
            ->field('id')->in($fileIds)
            ->sort('order', 'ASC')
            ->getQuery()->execute();
    }

    public function getActiveFile($fileId)
    {
        return $this->dm->createQueryBuilder(File::class)
            ->field('deleted')->notEqual(true)
            ->field('id')->equals($fileId)
            ->getQuery()->getSingleResult();
    }


    public function findAllByUser(User $user)
    {
        return $this->dm->createQueryBuilder(File::class)
            ->field('user')->equals($user)
            ->getQuery()->execute();
    }

    public function findAllBySite(Site $site)
    {
        return $this->dm->createQueryBuilder(File::class)
            ->field('site')->equals($site)
            ->getQuery()->execute();
    }
}
