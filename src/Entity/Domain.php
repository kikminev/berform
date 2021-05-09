<?php

namespace App\Entity;

use App\Repository\DomainRepository;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;

/**
 * @ORM\Entity(repositoryClass=DomainRepository::class)
 */
class Domain
{
    use TimestampableEntity;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=UserCustomer::class, inversedBy="domains")
     * @ORM\JoinColumn(nullable=false)
     */
    private $userCustomer;

    /**
     * @ORM\OneToOne(targetEntity=Site::class, inversedBy="domain", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=true)
     */
    private $site;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $name;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $isActive;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $ns1;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $ns2;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $cloudflareZoneId;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUserCustomer(): ?UserCustomer
    {
        return $this->userCustomer;
    }

    public function setUserCustomer(?UserCustomer $userCustomer): self
    {
        $this->userCustomer = $userCustomer;

        return $this;
    }

    public function getSite(): ?Site
    {
        return $this->site;
    }

    public function setSite(Site $site): self
    {
        $this->site = $site;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getIsActive(): ?bool
    {
        return $this->isActive;
    }

    public function setIsActive(?bool $isActive): self
    {
        $this->isActive = $isActive;

        return $this;
    }

    public function getNs1(): ?string
    {
        return $this->ns1;
    }

    public function setNs1(?string $ns1): self
    {
        $this->ns1 = $ns1;

        return $this;
    }

    public function getNs2(): ?string
    {
        return $this->ns2;
    }

    public function setNs2(?string $ns2): self
    {
        $this->ns2 = $ns2;

        return $this;
    }

    public function getCloudflareZoneId(): ?string
    {
        return $this->cloudflareZoneId;
    }

    public function setCloudflareZoneId(?string $cloudflareZoneId): self
    {
        $this->cloudflareZoneId = $cloudflareZoneId;

        return $this;
    }
}
