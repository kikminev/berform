<?php

namespace App\Repository;

use App\Entity\Post;
use App\Entity\Site;
use App\Entity\UserCustomer;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Post|null find($id, $lockMode = null, $lockVersion = null)
 * @method Post|null findOneBy(array $criteria, array $orderBy = null)
 * @method Post[]    findAll()
 * @method Post[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PostRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Post::class);
    }

    // /**
    //  * @return Post[] Returns an array of Post objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    public function findAllByUserSite(UserCustomer $user, Site $site): array
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.userCustomer = :user')
            ->andWhere('p.site = :site')
            ->setParameter('user', $user)
            ->setParameter('site', $site)
            ->getQuery()
            ->getResult();
    }
    public function findActivePostsBySite(Site $site, int $limit = null): array
    {
        $qb = $this->createQueryBuilder('p');

        if($limit) {
            $qb->setMaxResults($limit);
        }

        return $qb
            ->andWhere('p.site = :site')
//            ->andWhere('p.featuredParallax = :site')
            ->andWhere('p.isActive = true')
            ->orderBy('p.publishedAt', 'DESC')
            ->setParameter('site', $site->getId())
            ->getQuery()
            ->getResult();
    }

    public function findActiveBySlug(string $slug, Site $site)
    {
        $qb = $this->createQueryBuilder('p');

        return $qb
            ->andWhere('p.site = :site')
            ->andWhere('p.slug = :slug')
            ->andWhere('p.isDeleted = false OR p.isDeleted IS NULL')
            ->andWhere('p.isActive = true')
            ->setParameter('site', $site)
            ->setParameter('slug', $slug)
            ->orderBy('p.publishedAt', 'DESC')
            ->getQuery()
            ->getSingleResult();
    }

    public function findReadMorePosts(Site $site): array
    {
        $posts = $this->findActivePostsBySite($site, 2);

        $allHaveImages = true;
        /** @var Post $post */
        foreach ($posts as $post) {
            if (null === $post->getDefaultImage()) {
                $allHaveImages = false;
                break;
            }
        }

        return ['posts' => $posts, 'allHaveImages' => $allHaveImages];
    }

    public function deleteAllBySite(Site $site):void {
        $qb = $this->createQueryBuilder('p');
        $deleteQuery = $qb->delete('App:Post', 'p')->where('p.site = :siteId')->setParameter('siteId', $site->getId())->getQuery();
        $deleteQuery->execute();
    }
}
