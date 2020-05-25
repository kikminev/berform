<?php

namespace App\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @MongoDB\Document
 */
class Domain
{
    use TimestampableEntity;

    public function __clone()
    {
        $this->id = null;
    }

    /**
     * @MongoDB\Id
     */
    protected $id;

    /**
     * @var User $user
     * @MongoDB\ReferenceOne(targetDocument="User", storeAs="id")
     */
    private User $user;

    /**
     * @var Site $site
     * @MongoDB\ReferenceOne(targetDocument="Site", storeAs="id")
     */
    private $site;

    /**
     * @MongoDB\Field(type="string")
     * @Assert\NotBlank()
     */
    private string $name = '';

    /**
     * @MongoDB\Field(type="bool")
     */
    private bool $active = false;

    /**
     * @var string $ns1
     * @MongoDB\Field(type="string")
     */
    private string $ns1 = '';

    /**
     * @var string $ns2
     * @MongoDB\Field(type="string")
     */
    private string $ns2 = '';

    /**
     * @var string $cloudflareZoneId
     * @MongoDB\Field(type="string")
     */
    private string $cloudflareZoneId = '';

    public function getId()
    {
        return $this->id;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): void
    {
        $this->user = $user;
    }

    public function getSite(): Site
    {
        return $this->site;
    }

    public function setSite(Site $site): void
    {
        $this->site = $site;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getActive(): ?bool
    {
        return $this->active;
    }

    public function setActive(bool $active): void
    {
        $this->active = $active;
    }

    /**
     * @return string
     */
    public function getNS1(): string
    {
        return $this->ns1;
    }

    /**
     * @param string $ns1
     */
    public function setNS1(string $ns1): void
    {
        $this->ns1 = $ns1;
    }

    /**
     * @return string
     */
    public function getNS2(): string
    {
        return $this->ns2;
    }

    /**
     * @param string $ns2
     */
    public function setNS2(string $ns2): void
    {
        $this->ns2 = $ns2;
    }

    /**
     * @return string
     */
    public function getCloudflareZoneId(): string
    {
        return $this->cloudflareZoneId;
    }

    /**
     * @param string $cloudflareZoneId
     */
    public function setCloudflareZoneId(string $cloudflareZoneId): void
    {
        $this->cloudflareZoneId = $cloudflareZoneId;
    }
}
