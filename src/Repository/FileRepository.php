<?php

namespace App\Repository;

use App\Entity\Album;
use App\Entity\File;
use App\Entity\Page;
use App\Entity\Post;
use App\Entity\Site;
use App\Entity\UserCustomer;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

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


    public function findAllBySite(Site $site)
    {

        $qb = $this->createQueryBuilder('f');

        return $qb->andWhere('f.site = :site')
            ->setParameter('site', $site->getId())
            ->getQuery()
            ->getResult();
    }

    public function findAllActiveBySite(Site $site)
    {

        $qb = $this->createQueryBuilder('f');

        return $qb->andWhere('f.site = :site')
            ->andWhere($qb->expr()->not($qb->expr()->neq('f.isDeleted', ':isDeleted')))
            ->setParameter('site', $site->getId())
            ->setParameter('isDeleted', false)
            ->getQuery()
            ->getResult();
    }

    public function findAllActiveByPage(Page $page)
    {
        $qb = $this->createQueryBuilder('f');

        return $qb->andWhere('f.page = :page')
            ->andWhere($qb->expr()->not($qb->expr()->neq('f.isDeleted', ':isDeleted')))
            ->setParameter('page', $page->getId())
            ->setParameter('isDeleted', false)
            ->getQuery()
            ->getResult();
    }

    public function findActiveByIds(array $ids, UserCustomer $user)
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

    public function findAllActiveByAlbumAndSite(Album $album, Site $site)
    {
        $qb = $this->createQueryBuilder('f');

        return $qb
            ->andWhere('f.album = :album')
            ->andWhere('f.site = :site')
            ->andWhere('f.isDeleted = false OR f.isDeleted IS NULL')
            ->setParameter('site', $site)
            ->setParameter('album', $album)
            ->orderBy('f.sequenceOrder', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findAllActiveByPostAndSite(Post $post, Site $site)
    {
        $qb = $this->createQueryBuilder('f');

        return $qb
            ->andWhere('f.post = :post')
            ->andWhere('f.site = :site')
            ->andWhere($qb->expr()->not($qb->expr()->neq('f.isDeleted', ':isDeleted')))
            ->setParameter('isDeleted', false)
            ->setParameter('post', $post)
            ->setParameter('site', $site)
            ->orderBy('f.sequenceOrder', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
