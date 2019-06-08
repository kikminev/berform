<?php

namespace App\Document\Payment;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

/**
 * @MongoDB\Document
 */
class Subscription
{
    /**
     * @MongoDB\Id
     */
    protected $id;

    /**
     * @var Product $product
     * @MongoDB\ReferenceOne(targetDocument="Prod", inversedBy="domains")
     */
    protected $user;

}
