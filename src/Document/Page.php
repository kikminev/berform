<?php

namespace App\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @MongoDB\Document
 */
class Page
{
    /**
     * @MongoDB\Id
     */
    private $id;

    /**
     * @MongoDB\ReferenceOne(targetDocument="Site")
     */
    private $site;

    /**
     * @var User $user
     * @MongoDB\ReferenceOne(targetDocument="User", inversedBy="pages")
     */
    private $user;

    /**
     * @MongoDB\Field(type="string")
     */
    private $name;

    /**
     * @MongoDB\Field(type="string")
     */
    private $slug;

    /**
     * @MongoDB\Field(type="int")
     */
    private $order;

    /**
     * @MongoDB\Field(type="string")
     */
    private $locale;

    /**
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
     * @var \DateTime $updatedAt
     *
     * @MongoDB\Date
     * @Gedmo\Timestampable(on="update")
     */
    private $updatedAt;

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
     * @param mixed $id
     */
    public function setId($id): void
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getSite()
    {
        return $this->site;
    }

    /**
     * @param mixed $site
     */
    public function setSite($site): void
    {
        $this->site = $site;
    }

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
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * @param mixed $slug
     */
    public function setSlug($slug): void
    {
        $this->slug = $slug;
    }

    /**
     * @return mixed
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * @param mixed $locale
     */
    public function setLocale($locale): void
    {
        $this->locale = $locale;
    }

    /**
     * @return mixed
     */
    public function getActive()
    {
        return $this->active;
    }

    /**
     * @param mixed $active
     */
    public function setActive($active): void
    {
        $this->active = $active;
    }

    /**
     * @return int
     */
    public function getOrder(): int
    {
        return $this->order;
    }

    /**
     * @param int $order
     */
    public function setOrder(int $order): void
    {
        $this->order = $order;
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
}
