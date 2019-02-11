<?php

namespace App\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @MongoDB\Document
 */
class File
{
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
     * @var User $user
     * @MongoDB\ReferenceOne(targetDocument="User", inversedBy="files")
     */
    private $user;

    /**
     * @MongoDB\ReferenceOne(targetDocument="Page")
     */
    protected $page;

    /**
     * @var \DateTime $updatedAt
     * @MongoDB\Date
     * @Gedmo\Timestampable(on="update")
     */
    protected $updatedAt;

    /**
     * @var \DateTime $createdAt
     * @MongoDB\Date
     * @Gedmo\Timestampable(on="create")
     */
    protected $createdAt;

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
     * @return \DateTime
     */
    public function getUpdatedAt(): \DateTime
    {
        return $this->updatedAt;
    }

    /**
     * @param \DateTime $updatedAt
     */
    public function setUpdatedAt(\DateTime $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    /**
     * @param \DateTime $createdAt
     */
    public function setCreatedAt(\DateTime $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @return Page
     */
    public function getPage(): Page
    {
        return $this->page;
    }

    /**
     * @param Page $page
     */
    public function setPage(Page $page): void
    {
        $this->page = $page;
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
        return $this->deleted;
    }

    /**
     * @param bool $deleted
     */
    public function setDeleted(bool $deleted): void
    {
        $this->deleted = $deleted;
    }
}
