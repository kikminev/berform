<?php

namespace App\Document\Payment;

use App\Document\Site;
use App\Document\User;
use DateTime;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Gedmo\Timestampable\Traits\TimestampableDocument;

/**
 * @MongoDB\Document
 */
class Subscription
{
    use TimestampableDocument;
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
     * @var Site $site
     * @MongoDB\ReferenceOne(targetDocument="\App\Document\Site", storeAs="id")
     */
    protected $site;

    /**
     * @var \DateTime
     * @MongoDB\Date
     */
    protected $expiresAt;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

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
    public function getExpiresAt(): ?DateTime
    {
        return $this->expiresAt;
    }

    /**
     * @param \DateTime $expiresAt
     */
    public function setExpiresAt(? DateTime $expiresAt): void
    {
        $this->expiresAt = $expiresAt;
    }

    /**
     * @return Site
     */
    public function getSite(): Site
    {
        return $this->site;
    }

    /**
     * @param Site $site
     */
    public function setSite(Site $site): void
    {
        $this->site = $site;
    }
}
