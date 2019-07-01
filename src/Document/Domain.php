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

    /**
     * @MongoDB\Id
     */
    protected $id;

    /**
     * @var User $user
     * @MongoDB\ReferenceOne(targetDocument="User", storeAs="id")
     */
    protected $user;

    /**
     * @var Site $site
     * @MongoDB\ReferenceOne(targetDocument="Site", storeAs="id")
     */
    protected $site;

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


    /**
     * @MongoDB\Field(type="string")
     * @Assert\NotBlank()
     */
    protected $name;

    /**
     * @MongoDB\Field(type="string")
     */
    protected $cloudflareZoneId;

    /**
     * @MongoDB\Field(type="string")
     */
    protected $nameServers;

    /**
     * @var boolean|null $active
     * @MongoDB\Field(type="bool")
     */
    protected $active;

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name): void
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getCloudflareZoneId()
    {
        return $this->cloudflareZoneId;
    }

    /**
     * @param mixed $cloudflareZoneId
     */
    public function setCloudflareZoneId($cloudflareZoneId): void
    {
        $this->cloudflareZoneId = $cloudflareZoneId;
    }

    /**
     * @return mixed
     */
    public function getNameServers()
    {
        return $this->nameServers;
    }

    /**
     * @param mixed $nameServers
     */
    public function setNameServers($nameServers): void
    {
        $this->nameServers = $nameServers;
    }

    /**
     * @return bool|null
     */
    public function getActive(): ?bool
    {
        return $this->active;
    }

    /**
     * @param bool|null $active
     */
    public function setActive(?bool $active): void
    {
        $this->active = $active;
    }
}
