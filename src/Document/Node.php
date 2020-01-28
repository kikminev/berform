<?php

namespace App\Document;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @MongoDB\Document
 */
class Node
{
    public function __construct()
    {
        $this->files = new ArrayCollection();
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
     * @MongoDB\Field(type="string")
     */
    private $type;

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
    private $translatedMetaDescription = [];

    /**
     * @var null|array $files
     * @MongoDB\ReferenceMany(targetDocument="File", storeAs="id")
     */
    private $files;

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
     * @return string
     */
    public function getName():? string
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

    /**
     * @return Page|null
     */
    public function getParent(): ?Page
    {
        return $this->parent;
    }

    /**
     * @param Page|null $parent
     */
    public function setParent(?Page $parent): void
    {
        $this->parent = $parent;
    }

    public function getFiles()
    {
        return $this->files;
    }

    public function addFile(File $file): void
    {
        $this->files[] = $file;
    }

    public function setFiles($files): void
    {
        $this->files = $files;
    }

    /**
     * @return mixed
     */
    public function getSite(): Site
    {
        return $this->site;
    }

    /**
     * @param mixed $site
     */
    public function setSite(Site $site): void
    {
        $this->site = $site;
    }

    public function __toString():string
    {
        $name = '';
        if(null === $this->getName()) {
            return $name;
        }

        return $name;
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param mixed $type
     */
    public function setType($type): void
    {
        $this->type = $type;
    }


    public function __clone()
    {
        $this->id = null;
    }
}
