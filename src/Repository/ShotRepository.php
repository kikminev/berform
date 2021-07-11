<?php

namespace App\Repository;

use App\Entity\Shot;
use App\Entity\Site;
use App\Entity\UserCustomer;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Shot|null find($id, $lockMode = null, $lockVersion = null)
 * @method Shot|null findOneBy(array $criteria, array $orderBy = null)
 * @method Shot[]    findAll()
 * @method Shot[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ShotRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Shot::class);
    }

    public function getActiveByUserSite(UserCustomer $user, Site $site)
    {
        $qb = $this->createQueryBuilder('s');

        return $qb
            ->andWhere('s.userCustomer = :user')
            ->andWhere('s.site = :site')
            ->andWhere('s.isDeleted = false OR s.isDeleted IS NULL')
            ->setParameter('user', $user)
            ->setParameter('site', $site)
            ->orderBy('s.sequenceOrder', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function getActiveBySite(Site $site)
    {
        $qb = $this->createQueryBuilder('s');

        return $qb
            ->andWhere('s.site = :site')
            ->andWhere('s.isDeleted = false OR s.isDeleted IS NULL')
            ->innerJoin('s.files', 'files')
            ->setParameter('site', $site)
            ->orderBy('s.sequenceOrder', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function getShotFiles(Shot $shot)
    {
        $qb = $this->createQueryBuilder('s');

        return $qb
            ->innerJoin('s.files', 'files')
            ->andWhere('s.id = :shot')
            ->andWhere('s.isDeleted = false OR s.isDeleted IS NULL')
            ->setParameter('shot', $shot->getId())
            ->orderBy('s.sequenceOrder', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
