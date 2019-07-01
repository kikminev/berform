<?php

namespace App\Document\Payment;

use App\Document\User;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Gedmo\Timestampable\Traits\TimestampableEntity;

/**
 * @MongoDB\Document
 */
class Subscription
{
    use TimestampableEntity;

    /**
     * @MongoDB\Id
     */
    private $id;

    /**
     * @var Product $product
     * @MongoDB\ReferenceOne(targetDocument="Product", storeAs="id")
     */
    private $product;

    /**
     * @var User $user
     * @MongoDB\ReferenceOne(targetDocument="\App\Document\User", storeAs="id")
     */
    protected $user;

    /**
     * @var \DateTime
     * @ORM\Column(type="datetime")
     */
    protected $expiresAt;

    /**
     * @return Product
     */
    public function getProduct(): Product
    {
        return $this->product;
    }

    /**
     * @param Product $product
     */
    public function setProduct(Product $product): void
    {
        $this->product = $product;
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @param User $user
     */
    public function setUser(User $user): void
    {
        $this->user = $user;
    }

    /**
     * @return \DateTime
     */
    public function getExpiresAt(): \DateTime
    {
        return $this->expiresAt;
    }

    /**
     * @param \DateTime $expiresAt
     */
    public function setExpiresAt(\DateTime $expiresAt): void
    {
        $this->expiresAt = $expiresAt;
    }
}
