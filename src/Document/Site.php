<?php

namespace App\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use App\Validation\Site\Host as HostAssert;

/**
 * @MongoDB\Document
 */
class Site
{
    use TimestampableEntity;

    /**
     * @MongoDB\Id
     */
    protected $id;

    /** @MongoDB\ReferenceMany(targetDocument="Page", storeAs="id") */
    private $pages;

    /**
     * @var User $user
     * @MongoDB\ReferenceOne(targetDocument="User", storeAs="id")
     */
    protected $user;

    /**
     * @var Domain|null $domain
     * @MongoDB\ReferenceOne(targetDocument="Domain", storeAs="id")
     */
    protected $domain;

    /**
     * @var string|null
     * @MongoDB\Field(type="string")
     */
    protected $slug;

    /**
     * @MongoDB\Field(type="string")
     * @Assert\NotBlank()
     */
    protected $name;

    /**
     * @MongoDB\Field(type="string")
     */
    protected $logo;

    /**
     * @MongoDB\Field(type="string")
     */
    protected $facebook;

    /**
     * @MongoDB\Field(type="string")
     */
    protected $email;

    /**
     * @MongoDB\Field(type="string")
     */
    protected $instagram;

    /**
     * @MongoDB\Field(type="string")
     */
    protected $twitter;

    /**
     * @var string|null
     * @MongoDB\Field(type="string")
     */
    protected $linkedIn;

    /**
     * @var array $translatedAddress
     * @MongoDB\Field(type="hash")
     */
    private $translatedAddress = [];

    /**
     * @var array $translatedTemplateName
     * @MongoDB\Field(type="hash")
     */
    private $translatedTemplateName = [];

    /**
     * @var array $translatedDescription
     * @MongoDB\Field(type="hash")
     */
    private $translatedDescription = [];

    /**
     * @var null|string $workingFrom
     * @MongoDB\Field(type="string")
     */
    protected $workingFrom;

    /**
     * @var null|string $workingTo
     * @MongoDB\Field(type="string")
     */
    protected $workingTo;

    /**
     * @var string $phone
     * @MongoDB\Field(type="string")
     */
    protected $phone;

    /**
     * @MongoDB\Field(type="bool")
     */
    protected $active;

    /**
     * @MongoDB\Field(type="bool")
     */
    protected $archived;

    /**
     * @var bool $isTemplate
     * @MongoDB\Field(type="bool")
     */
    protected $isTemplate;

    /**
     * @var string $theme
     * @MongoDB\Field(type="string")
     */
    protected $theme;

    /**
     * @var string $template
     * @MongoDB\Field(type="string")
     */
    protected $template;

    /**
     * @var string $category
     * @MongoDB\Field(type="string")
     */
    protected $category;

    /**
     * @var null|string $customHtml
     * @MongoDB\Field(type="string")
     */
    protected $customHtml;

    /**
     * @return string
     */
    public function getTheme(): string
    {
        return $this->theme;
    }

    /**
     * @param string $theme
     */
    public function setTheme(string $theme): void
    {
        $this->theme = $theme;
    }

    /**
     * @var string|null $defaultImage
     * @MongoDB\Field(type="string")
     */
    protected $defaultImage;

    /**
     * @var string|null $previewUrl
     * @MongoDB\Field(type="string")
     */
    protected $previewUrl;

    /**
     * @var bool $published
     * @MongoDB\Field(type="bool")
     */
    protected $published;

    /**
     * @MongoDB\Field(type="bool")
     */
    protected $deleted;

    /**
     * @var string $host
     * @MongoDB\Field(type="string")
     * @HostAssert\HostIsUnique
     */
    protected $host;

    /**
     * @var null|string $defaultLanguage
     * @MongoDB\Field(type="string")
     */
    protected $defaultLanguage;

    /**
     * @var array $supportedLanguages
     * @MongoDB\Field(type="hash")
     */
    protected $supportedLanguages = [];


    /**
     * @var null|string $customCss
     * @MongoDB\Field(type="string")
     */
    protected $customCss;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return null|string
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return null|string
     */
    public function getLogo(): ?string
    {
        return $this->logo;
    }

    /**
     * @param string $logo
     */
    public function setLogo(string $logo): void
    {
        $this->logo = $logo;
    }

    /**
     * @return null|string
     */
    public function getFacebook(): ?string
    {
        return $this->facebook;
    }

    /**
     * @param string $facebook
     */
    public function setFacebook(string $facebook): void
    {
        $this->facebook = $facebook;
    }

    /**
     * @return null|string
     */
    public function getInstagram(): ?string
    {
        return $this->instagram;
    }

    /**
     * @param string $instagram
     */
    public function setInstagram(string $instagram): void
    {
        $this->instagram = $instagram;
    }

    /**
     * @return null|string
     */
    public function getTwitter(): ?string
    {
        return $this->twitter;
    }

    /**
     * @param string $twitter
     */
    public function setTwitter(string $twitter): void
    {
        $this->twitter = $twitter;
    }

    /**
     * @return bool
     */
    public function getActive(): bool
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
     * @return bool
     */
    public function getDeleted(): ?bool
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

    /**
     * @return bool
     */
    public function isPublished(): bool
    {
        return $this->published;
    }

    /**
     * @param bool $published
     */
    public function setPublished(bool $published): void
    {
        $this->published = $published;
    }

    /**
     * @return string
     */
    public function getPhone(): string
    {
        return $this->phone;
    }

    /**
     * @param string $phone
     */
    public function setPhone(string $phone): void
    {
        $this->phone = $phone;
    }

    /**
     * @return bool|null
     */
    public function isTemplate(): ?bool
    {
        return $this->isTemplate;
    }

    /**
     * @param bool $isTemplate
     */
    public function setIsTemplate(bool $isTemplate): void
    {
        $this->isTemplate = $isTemplate;
    }

    /**
     * @return string
     */
    public function getHost(): string
    {
        return $this->host;
    }

    /**
     * @param string $host
     */
    public function setHost(string $host): void
    {
        $this->host = $host;
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
    public function getTranslatedAddress(): array
    {
        return $this->translatedAddress;
    }

    /**
     * @param array $translatedAddress
     */
    public function setTranslatedAddress(array $translatedAddress): void
    {
        $this->translatedAddress = $translatedAddress;
    }

    /**
     * @return array
     */
    public function getTranslatedDescription(): array
    {
        return $this->translatedDescription;
    }

    /**
     * @param array $translatedDescription
     */
    public function setTranslatedDescription(array $translatedDescription): void
    {
        $this->translatedDescription = $translatedDescription;
    }

    /**
     * @return null|string
     */
    public function getWorkingFrom(): ?string
    {
        return $this->workingFrom;
    }

    /**
     * @param null|string $workingFrom
     */
    public function setWorkingFrom(?string $workingFrom): void
    {
        $this->workingFrom = $workingFrom;
    }

    /**
     * @return null|string
     */
    public function getWorkingTo(): ?string
    {
        return $this->workingTo;
    }

    /**
     * @param null|string $workingTo
     */
    public function setWorkingTo(?string $workingTo): void
    {
        $this->workingTo = $workingTo;
    }

    /**
     * @return Domain|null
     */
    public function getDomain(): ?Domain
    {
        return $this->domain;
    }

    /**
     * @param Domain|null $domain
     */
    public function setDomain(?Domain $domain): void
    {
        $this->domain = $domain;
    }

    public function __toString()
    {
        return $this->getName() ?? '';
    }

    /**
     * @return null|string
     */
    public function getDefaultLanguage(): ?string
    {
        return $this->defaultLanguage;
    }

    /**
     * @param null|string $defaultLanguage
     */
    public function setDefaultLanguage(?string $defaultLanguage): void
    {
        $this->defaultLanguage = $defaultLanguage;
    }

    /**
     * @return array
     */
    public function getSupportedLanguages(): array
    {
        return $this->supportedLanguages;
    }

    /**
     * @param array $supportedLanguages
     */
    public function setSupportedLanguages(array $supportedLanguages): void
    {
        $this->supportedLanguages = $supportedLanguages;
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
     * @return null|string
     */
    public function getDefaultImage(): ?string
    {
        return $this->defaultImage;
    }

    /**
     * @param null|string $defaultImage
     */
    public function setDefaultImage(?string $defaultImage): void
    {
        $this->defaultImage = $defaultImage;
    }

    /**
     * @return mixed
     */
    public function getPages()
    {
        return $this->pages;
    }

    /**
     * @param mixed $pages
     */
    public function setPages($pages): void
    {
        $this->pages = $pages;
    }

    public function __clone()
    {
        $this->id = null;
    }

    /**
     * @return string|null
     */
    public function getCustomCss(): ?string
    {
        return $this->customCss;
    }

    /**
     * @param string|null $customCss
     */
    public function setCustomCss(?string $customCss): void
    {
        $this->customCss = $customCss;
    }

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param mixed $email
     */
    public function setEmail($email): void
    {
        $this->email = $email;
    }

    /**
     * @return string
     */
    public function getTemplate(): string
    {
        return $this->template;
    }

    /**
     * @param string $template
     */
    public function setTemplate(string $template): void
    {
        $this->template = $template;
    }

    /**
     * @return string|null
     */
    public function getCustomHtml(): ?string
    {
        return $this->customHtml;
    }

    /**
     * @param string|null $customHtml
     */
    public function setCustomHtml(?string $customHtml): void
    {
        $this->customHtml = $customHtml;
    }

    /**
     * @return string|null
     */
    public function getLinkedIn(): ?string
    {
        return $this->linkedIn;
    }

    /**
     * @param string|null $linkedIn
     */
    public function setLinkedIn(?string $linkedIn): void
    {
        $this->linkedIn = $linkedIn;
    }

    /**
     * @return string
     */
    public function getCategory(): string
    {
        return $this->category;
    }

    /**
     * @param string $category
     */
    public function setCategory(string $category): void
    {
        $this->category = $category;
    }

    /**
     * @return mixed
     */
    public function getArchived()
    {
        return $this->archived;
    }

    /**
     * @param mixed $archived
     */
    public function setArchived($archived): void
    {
        $this->archived = $archived;
    }

    /**
     * @return array
     */
    public function getTranslatedTemplateName(): array
    {
        return $this->translatedTemplateName;
    }

    /**
     * @param array $translatedTemplateName
     */
    public function setTranslatedTemplateName(array $translatedTemplateName): void
    {
        $this->translatedTemplateName = $translatedTemplateName;
    }

    /**
     * @return string|null
     */
    public function getPreviewUrl(): ?string
    {
        return $this->previewUrl;
    }

    /**
     * @param string|null $previewUrl
     */
    public function setPreviewUrl(?string $previewUrl): void
    {
        $this->previewUrl = $previewUrl;
    }
}
