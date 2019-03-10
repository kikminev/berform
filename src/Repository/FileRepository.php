<?php

namespace App\Repository;

use App\Document\File;
use App\Document\Page;
use App\Document\Post;
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
     * @param $page
     * @return mixed
     * @throws \Doctrine\ODM\MongoDB\MongoDBException
     */
    public function getPageFiles(Page $page)
    {

        return $this->dm->createQueryBuilder(File::class)
            ->field('page')->equals($page)
            ->field('deleted')->notEqual(true)
            ->sort('order', 'DESC')
            ->getQuery()->execute();
    }

    /**
     * @param $page
     * @return mixed
     * @throws \Doctrine\ODM\MongoDB\MongoDBException
     */
    public function getPostFiles(Post $post)
    {

        return $this->dm->createQueryBuilder(File::class)
            ->field('post')->equals($post)
            ->field('deleted')->notEqual(true)
            ->sort('order', 'DESC')
            ->getQuery()->execute();
    }
}
