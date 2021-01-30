<?php

namespace App\Repository;

use App\Entity\Album;
use App\Entity\Site;
use App\Entity\UserCustomer;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use function Doctrine\ORM\QueryBuilder;

/**
 * @method Album|null find($id, $lockMode = null, $lockVersion = null)
 * @method Album|null findOneBy(array $criteria, array $orderBy = null)
 * @method Album[]    findAll()
 * @method Album[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AlbumRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Album::class);
    }

    public function getActiveByUserSite(UserCustomer $user, Site $site)
    {
        $qb = $this->createQueryBuilder('a');

        return $qb
            ->andWhere('a.userCustomer = :user')
            ->andWhere('a.site = :site')
            ->andWhere('a.isDeleted = false OR a.isDeleted IS NULL')
            ->setParameter('user', $user)
            ->setParameter('site', $site)
            ->orderBy('a.sequenceOrder', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findAllBySite(Site $site)
    {
        $qb = $this->createQueryBuilder('a');

        return $qb
            ->andWhere('a.site = :site')
            ->andWhere('a.isDeleted = false OR a.isDeleted IS NULL')
            ->andWhere('a.isActive = true')
            ->setParameter('site', $site)
            ->orderBy('a.sequenceOrder', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findAllByUserSite(UserCustomer $user, Site $site)
    {
        $qb = $this->createQueryBuilder('a');

        return $qb
            ->andWhere('a.site = :site')
            ->andWhere('a.userCustomer = :user')
            ->setParameter('user', $user)
            ->setParameter('site', $site)
            ->orderBy('a.sequenceOrder', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
