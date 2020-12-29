<?php

namespace App\Entity\Billing;

use App\Repository\Billing\CurrencyRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CurrencyRepository::class)
 */
class Currency
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $systemCode;

    public function getId(): ?int
    {
        return $this->id;
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
}
