<?php

namespace App\Repository\Payment;

use App\Document\Payment\Product;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\DocumentRepository;

class ProductRepository extends DocumentRepository
{
    public function __construct(DocumentManager $dm)
    {
        parent::__construct($dm, $dm->getUnitOfWork(), $dm->getClassMetadata(Product::class));
    }

    public function findOneBySystemCode(string $systemCode): ?Product
    {
        /** @var Product $product */
        $product = $this->findOneBy(['systemCode' => $systemCode]);

        return $product;
    }

    /**
     * @param array $productIds
     * @return mixed
     * @throws \Doctrine\ODM\MongoDB\MongoDBException
     */
    public function findAllByIds(array $productIds)
    {
        return $this->createQueryBuilder()->field('id')->in($productIds)->getQuery()->execute();
    }
}
