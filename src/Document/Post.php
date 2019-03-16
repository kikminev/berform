<?php

namespace App\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @MongoDB\Document
 */
class Post
{
    public function __construct()
    {
        $this->files = new ArrayCollection();
    }

    /**
     * @MongoDB\Id
     */
    private $id;

    /**
     * @var Site $site
     * @MongoDB\ReferenceOne(targetDocument="Site")
     */
    private $site;

    /**
     * @var User $user
     * @MongoDB\ReferenceOne(targetDocument="User", inversedBy="pages")
     */
    private $user;

    /**
     * @var null|string $name
     * @MongoDB\Field(type="string")
     */
    private $name;

    /**
     * @var null|string $slug
     * @MongoDB\Field(type="string")
     */
    private $slug;

    /**
     * @var null|bool $active
     * @MongoDB\Field(type="boolean")
     */
    private $active;

    /**
     * @var array $translatedTitle
     * @MongoDB\Field(type="hash")
     */
    private $translatedTitle = array();

    /**
     * @var array $translatedContent
     * @MongoDB\Field(type="hash")
     */
    private $translatedContent = array();

    /**
     * @var array $translatedKeywords
     * @MongoDB\Field(type="hash")
     */
    private $translatedKeywords = array();

    /**
     * @var array $translatedMetaDescription
     * @MongoDB\Field(type="hash")
     */
    private $translatedMetaDescription = array();

    /**
     * @var null|array $files
     * @MongoDB\ReferenceMany(targetDocument="File", storeAs="id")
     */
    private $files = array();

    /**
     * @var \DateTime $updatedAt
     *
     * @MongoDB\Date
     * @Gedmo\Timestampable(on="update")
     */
    private $updatedAt;

    /**
     * @var \DateTime $publishedAt
     *
     * @MongoDB\Date
     */
    private $publishedAt;

    /**
     * @var \DateTime $createdAt
     *
     * @MongoDB\Date
     * @Gedmo\Timestampable(on="create")
     */
    private $createdAt;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
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
     * @return null|string
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param null|string $name
     */
    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return null|string
     */
    public function getSlug(): ?string
    {
        return $this->slug;
    }

    /**
     * @param null|string $slug
     */
    public function setSlug(?string $slug): void
    {
        $this->slug = $slug;
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

    /**
     * @return array
     */
    public function getTranslatedTitle(): array
    {
        return $this->translatedTitle;
    }

    /**
     * @param array $translatedTitle
     */
    public function setTranslatedTitle(array $translatedTitle): void
    {
        $this->translatedTitle = $translatedTitle;
    }

    /**
     * @return array
     */
    public function getTranslatedContent(): array
    {
        return $this->translatedContent;
    }

    /**
     * @param array $translatedContent
     */
    public function setTranslatedContent(array $translatedContent): void
    {
        $this->translatedContent = $translatedContent;
    }

    /**
     * @return array
     */
    public function getTranslatedKeywords(): array
    {
        return $this->translatedKeywords;
    }

    /**
     * @param array $translatedKeywords
     */
    public function setTranslatedKeywords(array $translatedKeywords): void
    {
        $this->translatedKeywords = $translatedKeywords;
    }

    /**
     * @return array
     */
    public function getTranslatedMetaDescription(): array
    {
        return $this->translatedMetaDescription;
    }

    /**
     * @param array $translatedMetaDescription
     */
    public function setTranslatedMetaDescription(array $translatedMetaDescription): void
    {
        $this->translatedMetaDescription = $translatedMetaDescription;
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
     * @return \DateTime
     */
    public function getPublishedAt(): \DateTime
    {
        return $this->publishedAt;
    }

    /**
     * @param \DateTime $publishedAt
     */
    public function setPublishedAt(\DateTime $publishedAt): void
    {
        $this->publishedAt = $publishedAt;
    }

    /**
     * @return array|null
     */
    public function getFiles(): ?array
    {
        return $this->files;
    }

    /**
     * @param array|null $files
     */
    public function setFiles(?array $files): void
    {
        $this->files = $files;
    }
}
