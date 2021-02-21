<?php

namespace App\Entity;

use App\Repository\ProductRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ProductRepository::class)
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
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $type;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $systemCode;

    /**
     * @ORM\Column(type="float")
     */
    private $price;

    /**
     * @ORM\ManyToOne(targetEntity=Currency::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $currency;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getSystemCode(): ?string
    {
        return $this->systemCode;
    }

    public function setSystemCode(string $systemCode): self
    {
        $this->systemCode = $systemCode;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getCurrency(): ?Currency
    {
        return $this->currency;
    }

    public function setCurrency(?Currency $currency): self
    {
        $this->currency = $currency;

        return $this;
    }
}
