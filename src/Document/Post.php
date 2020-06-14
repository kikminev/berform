<?php

namespace App\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Doctrine\Common\Collections\ArrayCollection;
use \DateTime;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use App\Validation\Site\Slug\Post as SlugAssert;

/**
 * @MongoDB\Document
 */
class Post
{
    use TimestampableEntity;

    public function __construct()
    {
        $this->files = new ArrayCollection();
    }

    public function __clone()
    {
        $this->id = null;
    }

    /**
     * @MongoDB\Id
     */
    private $id;

    /** @MongoDB\ReferenceOne(targetDocument="Site", storeAs="id") */
    private $site;

    /**
     * @var User $user
     * @MongoDB\ReferenceOne(targetDocument="User", storeAs="id")
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
     *
     * @SlugAssert\SlugIsUnique
     */
    private $slug;

    /**
     * @var null|bool $active
     * @MongoDB\Field(type="boolean")
     */
    private $active;

    /**
     * @var null|bool $deleted
     * @MongoDB\Field(type="boolean")
     */
    private $deleted;

    /**
     * @var null|bool $featuredParallax
     * @MongoDB\Field(type="boolean")
     */
    private $featuredParallax;

    /**
     * @var null|array $translatedTitle
     * @MongoDB\Field(type="hash")
     */
    private $translatedTitle = [];

    /**
     * @var null|array $translatedTitle
     * @MongoDB\Field(type="hash")
     */
    private $translatedExcerpt = [];

    /**
     * @var null|array $translatedContent
     * @MongoDB\Field(type="hash")
     */
    private $translatedContent = [];

    /**
     * @var null|array $translatedKeywords
     * @MongoDB\Field(type="hash")
     */
    private $translatedKeywords = [];

    /**
     * @var null|array $translatedMetaDescription
     * @MongoDB\Field(type="hash")
     */
    private $translatedMetaDescription;

    /**
     * @var null|array $files
     * @MongoDB\ReferenceMany(targetDocument="File", storeAs="id")
     */
    private $files = array();

    /**
     * @var DateTime $publishedAt
     *
     * @MongoDB\Date
     */
    private $publishedAt;

    /**
     * @var null|File $defaultImage
     * @MongoDB\ReferenceOne(targetDocument="File", storeAs="id")
     */
    private $defaultImage;

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
    public function isActive(): ?bool
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
     * @return array|null
     */
    public function getTranslatedTitle(): ?array
    {
        return $this->translatedTitle;
    }

    /**
     * @param array|null $translatedTitle
     */
    public function setTranslatedTitle(?array $translatedTitle): void
    {
        $this->translatedTitle = $translatedTitle;
    }

    /**
     * @return array|null
     */
    public function getTranslatedContent(): ?array
    {
        return $this->translatedContent;
    }

    /**
     * @param array|null $translatedContent
     */
    public function setTranslatedContent(?array $translatedContent): void
    {
        $this->translatedContent = $translatedContent;
    }

    /**
     * @return array|null
     */
    public function getTranslatedKeywords(): ?array
    {
        return $this->translatedKeywords;
    }

    /**
     * @param array|null $translatedKeywords
     */
    public function setTranslatedKeywords(?array $translatedKeywords): void
    {
        $this->translatedKeywords = $translatedKeywords;
    }

    /**
     * @return array|null
     */
    public function getTranslatedMetaDescription(): ?array
    {
        return $this->translatedMetaDescription;
    }

    /**
     * @param array|null $translatedMetaDescription
     */
    public function setTranslatedMetaDescription(?array $translatedMetaDescription): void
    {
        $this->translatedMetaDescription = $translatedMetaDescription;
    }

    /**
     * @return DateTime
     */
    public function getPublishedAt(): DateTime
    {
        return $this->publishedAt;
    }

    /**
     * @param DateTime $publishedAt
     */
    public function setPublishedAt(DateTime $publishedAt): void
    {
        $this->publishedAt = $publishedAt;
    }

    /**
     * @return array|null
     */
    public function getFiles()
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

    /**
     * @return array|null
     */
    public function getTranslatedExcerpt(): ?array
    {
        return $this->translatedExcerpt;
    }

    /**
     * @param array|null $translatedExcerpt
     */
    public function setTranslatedExcerpt(?array $translatedExcerpt): void
    {
        $this->translatedExcerpt = $translatedExcerpt;
    }

    /**
     * @return bool|null
     */
    public function getFeaturedParallax(): ?bool
    {
        return $this->featuredParallax;
    }

    /**
     * @param bool|null $featuredParallax
     */
    public function setFeaturedParallax(?bool $featuredParallax): void
    {
        $this->featuredParallax = $featuredParallax;
    }

    /**
     * @return File|null
     */
    public function getDefaultImage(): ?File
    {
        return $this->defaultImage;
    }

    /**
     * @param File|null $defaultImage
     */
    public function setDefaultImage(?File $defaultImage): void
    {
        $this->defaultImage = $defaultImage;
    }

    /**
     * @return bool|null
     */
    public function isDeleted(): ?bool
    {
        return $this->deleted;
    }

    /**
     * @param bool|null $deleted
     */
    public function setDeleted(?bool $deleted): void
    {
        $this->deleted = $deleted;
    }
}
