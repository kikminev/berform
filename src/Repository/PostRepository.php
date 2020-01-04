<?php

namespace App\Repository;

use App\Document\Post;
use App\Document\Site;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\DocumentRepository;

class PostRepository extends DocumentRepository
{
    public function __construct(DocumentManager $dm)
    {
        parent::__construct($dm, $dm->getUnitOfWork(), $dm->getClassMetadata(Post::class));
    }

    /**
     * @param Site $site
     * @return mixed
     * @throws \Doctrine\ODM\MongoDB\MongoDBException
     */
    public function findActivePosts(Site $site, $limit = false)
    {
        $qb = $this->createQueryBuilder()
            ->field('featuredParallax')->notEqual(true)
            ->field('active')->equals(true)
            ->field('site')->equals($site)
            ->sort('createdAt', 'DESC');

        if ($limit) {
            $qb->limit($limit);
        }

        return $qb->getQuery()->execute();
    }
}
