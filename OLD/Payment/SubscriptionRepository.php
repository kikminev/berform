<?php

namespace App\Repository\Payment;

use App\Document\Payment\Subscription;
use App\Document\Site;
use App\Document\User;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\DocumentRepository;

class SubscriptionRepository extends DocumentRepository
{
    public function __construct(DocumentManager $dm)
    {
        parent::__construct($dm, $dm->getUnitOfWork(), $dm->getClassMetadata(Subscription::class));
    }

    public function deleteAllByUser(User $user)
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        return $this->dm->createQueryBuilder(Subscription::class)
            ->remove()
            ->field('user')->equals($user)
            ->getQuery()->execute();
    }

    public function findAllBySite(Site $site)
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        return $this->dm->createQueryBuilder(Subscription::class)
            ->field('site')->equals($site)
            ->getQuery()->execute();
    }

    public function deleteAllBySite(Site $site)
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        return $this->dm->createQueryBuilder(Subscription::class)
            ->remove()
            ->field('site')->equals($site)
            ->getQuery()->execute();
    }
}

