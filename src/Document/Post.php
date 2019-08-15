<?php

namespace App\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Doctrine\Common\Collections\ArrayCollection;
use \DateTime;
use Gedmo\Timestampable\Traits\TimestampableEntity;

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
    private $translatedTitle = [];

    /**
     * @var array $translatedContent
     * @MongoDB\Field(type="hash")
     */
    private $translatedContent = [];

    /**
     * @var array $translatedKeywords
     * @MongoDB\Field(type="hash")
     */
    private $translatedKeywords = [];

    /**
     * @var array $translatedMetaDescription
     * @MongoDB\Field(type="hash")
     */
    private $translatedMetaDescription;

    /**
     * @var null|array $files
     * @MongoDB\ReferenceMany(targetDocument="File", storeAs="id")
     */
    private $files;

    /**
     * @var DateTime $publishedAt
     *
     * @MongoDB\Date
     */
    private $publishedAt;

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
