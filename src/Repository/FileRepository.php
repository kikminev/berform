<?php

namespace App\Repository;

use App\Document\File;
use App\Document\Page;
use App\Document\Post;
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
    public function getActive($fileIds, User $user)
    {
        return $this->dm->createQueryBuilder(File::class)
            ->field('user')->equals($user)
            ->field('deleted')->notEqual(true)
            ->field('id')->in($fileIds)
            ->getQuery()->execute();
    }
    /**
     * @param $fileId
     * @return mixed
     * @throws \Doctrine\ODM\MongoDB\MongoDBException
     */
    public function getActiveFile($fileId)
    {
        return $this->dm->createQueryBuilder(File::class)
            ->field('deleted')->notEqual(true)
            ->field('id')->equals($fileId)
            ->getQuery()->getSingleResult();
    }


    //public function getPageFiles($files)
    //{
    //    return $this->dm->createQueryBuilder(File::class)
    //        ->field('id')->in($files)
    //        ->field('deleted')->notEqual(true)
    //        ->sort('order', 'DESC')
    //        ->getQuery()->execute();
    //}
    //
    ///**
    // * @param $page
    // * @return mixed
    // * @throws \Doctrine\ODM\MongoDB\MongoDBException
    // */
    //public function getPostFiles(Post $post)
    //{
    //
    //    return $this->dm->createQueryBuilder(File::class)
    //        ->field('post')->equals($post)
    //        ->field('deleted')->notEqual(true)
    //        ->sort('order', 'DESC')
    //        ->getQuery()->execute();
    //}
}
