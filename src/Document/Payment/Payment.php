<?php

namespace App\Document\Payment;
use App\Document\User;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

/**
 * @MongoDB\Document
 */
class Payment
{
    /**
     * @MongoDB\Id
     */
    protected $id;

    /**
     * @var User $user
     * @MongoDB\ReferenceOne(targetDocument="User", inversedBy="domains")
     */
    protected $user;

    /**
     * @var Payment $product
     * @MongoDB\ReferenceOne(targetDocument="Product")
     */
    protected $product;

    /**
     * @var float $price
     * @MongoDB\Field(type="string")
     */
    protected $price;

    /**
     * @var string $data
     * @MongoDB\Field(type="string")
     */
    protected $data;

    /**
     * @var string $provider
     * @MongoDB\Field(type="string")
     */
    protected $provider;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
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
     * @return Payment
     */
    public function getProduct(): Payment
    {
        return $this->product;
    }

    /**
     * @param Payment $product
     */
    public function setProduct(Payment $product): void
    {
        $this->product = $product;
    }

    /**
     * @return float
     */
    public function getPrice(): float
    {
        return $this->price;
    }

    /**
     * @param float $price
     */
    public function setPrice(float $price): void
    {
        $this->price = $price;
    }

    /**
     * @return string
     */
    public function getData(): string
    {
        return $this->data;
    }

    /**
     * @param string $data
     */
    public function setData(string $data): void
    {
        $this->data = $data;
    }
}
