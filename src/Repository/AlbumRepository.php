<?php

namespace App\Repository;

use App\Document\Album;
use App\Document\Site;
use App\Document\User;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\DocumentRepository;

class AlbumRepository extends DocumentRepository
{
    public function __construct(DocumentManager $dm)
    {
        parent::__construct($dm, $dm->getUnitOfWork(), $dm->getClassMetadata(Album::class));
    }

    public function findAllByUserSite(User $User, Site $site)
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        return $this->dm->createQueryBuilder(Album::class)
            ->field('site')->equals($site)
            ->field('deleted')->notEqual(true)
            ->getQuery()->execute();
    }

    public function deleteAllBySite(Site $site)
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        return $this->dm->createQueryBuilder(Album::class)
            ->remove()
            ->field('site')->equals($site)
            ->getQuery()->execute();
    }
}
