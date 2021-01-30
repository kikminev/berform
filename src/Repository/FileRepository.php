<?php

namespace App\Repository;

use App\Entity\File;
use App\Entity\UserCustomer;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use function Doctrine\ORM\QueryBuilder;

/**
 * @method File|null find($id, $lockMode = null, $lockVersion = null)
 * @method File|null findOneBy(array $criteria, array $orderBy = null)
 * @method File[]    findAll()
 * @method File[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FileRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, File::class);
    }

    // /**
    //  * @return File[] Returns an array of File objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('f.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?File
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */


    public function getActiveByIds(array $ids, UserCustomer $user)
    {

        $qb = $this->createQueryBuilder('f');

        return $qb->andWhere('f.userCustomer = :user')
            ->andWhere($qb->expr()->not($qb->expr()->eq('f.isDeleted', ':isDeleted')))
            ->andWhere($qb->expr()->in('f.id', $ids))
            ->setParameter('user', $user)
            ->setParameter('isDeleted', true)
            ->orderBy('f.sequenceOrder', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
