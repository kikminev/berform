<?php

namespace App\Document\Payment;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

/**
 * @MongoDB\Document
 */

class Product
{
    public const PRODUCT_TYPE_DOMAIN = 'domain',
        PRODUCT_TYPE_HOSTING = 'hosting',
        PRODUCT_TYPE_FREE_HOSTING = 'free_hosting';

    public const PRODUCT_DOMAIN_BG = 'bg',
        PRODUCT_DOMAIN_COM = 'com',
        PRODUCT_DOMAIN_NET = 'net',
        PRODUCT_DOMAIN_BIZ = 'biz',
        PRODUCT_DOMAIN_EU = 'eu';

    public const PRODUCT_HOSTING = 'standard';

    /**
     * @MongoDB\Id
     */
    protected $id;

    /**
     * @var string $type
     * @MongoDB\Field(type="string")
     */
    protected $type;

    /**
     * @var string $name
     * @MongoDB\Field(type="string")
     */
    protected $name;

    /**
     * @var string $systemCode
     * @MongoDB\Field(type="string")
     */
    protected $systemCode;

    /**
     * @var int $period
     * @MongoDB\Field(type="int")
     */
    protected $period;

    /**
     * @var float $price
     * @MongoDB\Field(type="string")
     */
    protected $price;

    /**
     * @var Currency $currency
     * @MongoDB\ReferenceOne(targetDocument="Currency", storeAs="id")
     */
    private $currency;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType(string $type): void
    {
        $this->type = $type;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getSystemCode(): string
    {
        return $this->systemCode;
    }

    /**
     * @param string $systemCode
     */
    public function setSystemCode(string $systemCode): void
    {
        $this->systemCode = $systemCode;
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
     * @return Currency
     */
    public function getCurrency(): Currency
    {
        return $this->currency;
    }

    /**
     * @param Currency $currency
     */
    public function setCurrency(Currency $currency): void
    {
        $this->currency = $currency;
    }

    /**
     * @return int
     */
    public function getPeriod(): int
    {
        return $this->period;
    }

    /**
     * @param int $period
     */
    public function setPeriod(int $period): void
    {
        $this->period = $period;
    }
}
