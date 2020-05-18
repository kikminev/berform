<?php

namespace App\Repository;

use App\Document\Page;
use App\Document\Site;
use App\Document\User;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\Repository\DocumentRepository;

class PageRepository extends DocumentRepository
{
    public function __construct(DocumentManager $dm)
    {
        parent::__construct($dm, $dm->getUnitOfWork(), $dm->getClassMetadata(Page::class));
    }

    public function findActiveByIds($pageIds, User $user)
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        return $this->dm->createQueryBuilder(Page::class)
            ->field('user')->equals($user)
            ->field('deleted')->notEqual(true)
            ->field('id')->in($pageIds)
            ->getQuery()->execute();
    }

    public function findActiveByUserSite(User $user, Site $site)
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        return $this->dm->createQueryBuilder(Page::class)
            ->field('user')->equals($user)
            ->field('site')->equals($site->getId())
            ->field('deleted')->notEqual(true)
            ->getQuery()->execute();
    }

    public function findActiveBySite(Site $site)
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        return $this->dm->createQueryBuilder(Page::class)
            ->field('site')->equals($site->getId())
            ->field('deleted')->notEqual(true)
            ->field('active')->equals(true)
            ->sort('order')
            ->getQuery()->execute();
    }

    public function findActiveBySlug(string $slug, Site $site)
    {
        return $this->dm->createQueryBuilder(Page::class)
            ->field('site')->equals($site->getId())
            ->field('slug')->equals($slug)
            ->field('deleted')->notEqual(true)
            ->field('active')->equals(true)
            ->getQuery()->getSingleResult();
    }

    public function deleteAllBySite(Site $site)
    {
        return $this->dm->createQueryBuilder(Page::class)
            ->remove()
            ->field('site')->equals($site->getId())
            ->getQuery()->execute();
    }
}
