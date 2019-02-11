<?php

namespace App\Repository;

use App\Document\File;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\DocumentRepository;

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
    public function getPageFiles($page)
    {

        return $this->dm->createQueryBuilder(File::class)
            ->field('page')->equals($page)
            ->field('deleted')->notEqual(true)
            ->sort('order', 'DESC')
            ->getQuery()->execute();
    }
}
