<?php

namespace App\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Gedmo\Timestampable\Traits\TimestampableEntity;

/**
 * @MongoDB\Document
 */
class File
{
    use TimestampableEntity;

    /**
     * @MongoDB\Id
     */
    protected $id;

    /**
     * @var boolean $active
     * @MongoDB\Field(type="bool")
     */
    protected $active;

    /**
     * @var boolean $deleted
     * @MongoDB\Field(type="bool")
     */
    protected $deleted;

    /**
     * @var string $fileUrl
     * @MongoDB\Field(type="string")
     */
    protected $fileUrl;

    /**
     * @var string $baseName
     * @MongoDB\Field(type="string")
     */
    protected $baseName;

    /**
     * @var int|null $order
     * @MongoDB\Field(type="int")
     */
    protected $order;

    /**
     * @var User $user
     * @MongoDB\ReferenceOne(targetDocument="User", storeAs="id")
     */
    private $user;

    /**
     * @var null|Site $site
     * @MongoDB\ReferenceOne(targetDocument="Site", storeAs="id")
     */
    private $site;

    /**
     * @var null|Post $post
     * @MongoDB\ReferenceOne(targetDocument="Post", storeAs="id")
     */
    protected $post;

    public function getId()
    {
        return $this->id;
    }

    /**
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->active;
    }

    /**
     * @param bool $active
     */
    public function setActive(bool $active): void
    {
        $this->active = $active;
    }

    /**
     * @return string
     */
    public function getFileUrl(): ?string
    {
        return $this->fileUrl;
    }

    /**
     * @param string $fileUrl
     */
    public function setFileUrl(string $fileUrl): void
    {
        $this->fileUrl = $fileUrl;
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
     * @return bool
     */
    public function isDeleted(): bool
    {
        return (boolean) $this->deleted;
    }

    /**
     * @param bool $deleted
     */
    public function setDeleted(bool $deleted): void
    {
        $this->deleted = $deleted;
    }

    /**
     * @return Site|null
     */
    public function getSite(): ?Site
    {
        return $this->site;
    }

    /**
     * @param Site|null $site
     */
    public function setSite(?Site $site): void
    {
        $this->site = $site;
    }

    /**
     * @return Post|null
     */
    public function getPost(): ?Post
    {
        return $this->post;
    }

    /**
     * @param Post|null $post
     */
    public function setPost(?Post $post): void
    {
        $this->post = $post;
    }

    /**
     * @return string
     */
    public function getBaseName(): string
    {
        return $this->baseName;
    }

    /**
     * @param string $baseName
     */
    public function setBaseName(string $baseName): void
    {
        $this->baseName = $baseName;
    }

    public function getOrder(): ?int
    {
        return $this->order;
    }

    public function setOrder(int $order): void
    {
        $this->order = $order;
    }
}
