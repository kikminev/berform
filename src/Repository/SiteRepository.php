<?php

namespace App\Repository;

use App\Entity\UserCustomer;
use App\Entity\Site;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Site|null find($id, $lockMode = null, $lockVersion = null)
 * @method Site|null findOneBy(array $criteria, array $orderBy = null)
 * @method Site[]    findAll()
 * @method Site[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SiteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Site::class);
    }

    // /**
    //  * @return Site1[] Returns an array of Site1 objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Site1
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    public function getTemplates()
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.isTemplate = :value')
            ->setParameter('value', true)
            ->getQuery()
            ->getResult();
    }

    public function getByUser(UserCustomer $user)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.userCustomer = :value')
            ->setParameter('value', $user->getId())
            ->getQuery()
            ->getResult();
    }
}
