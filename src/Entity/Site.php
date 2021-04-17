<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\SiteRepository;
use Gedmo\Timestampable\Traits\TimestampableEntity;

/**
 * @ORM\Entity(repositoryClass=SiteRepository::class)
 */
class Site
{
    use TimestampableEntity;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $slug;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $isTemplate;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $previewUrl;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $defaultImage;

    /**
     * @ORM\Column(type="string", length=20, nullable=true)
     */
    private $defaultLanguage;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $host;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $category;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $template;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $facebook;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $instagram;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $linkedIn;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $twitter;

    /**
     * @ORM\ManyToOne(targetEntity=UserCustomer::class, inversedBy="sites")
     * @ORM\JoinColumn(nullable=false)
     */
    private $userCustomer;

    /**
     * @ORM\OneToMany(targetEntity=Page::class, mappedBy="site", orphanRemoval=true)
     */
    private $pages;

    /**
     * @ORM\Column(type="json", nullable=true)
     */
    private $supportedLanguages = [];

    /**
     * @ORM\OneToOne(targetEntity=Domain::class, mappedBy="site", cascade={"persist", "remove"})
     */
    private $domain;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $workingFrom;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $workingTo;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $customCss;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $customHtml;

    /**
     * @ORM\Column(type="json", nullable=true)
     */
    private $translatedTemplateName = [];

    /**
     * @ORM\Column(type="json", nullable=true)
     */
    private $translatedAddress = [];

    /**
     * @ORM\Column(type="json", nullable=true)
     */
    private $translatedDescription = [];

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $templateSystemCode;

    public function __construct()
    {
        $this->pages = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getIsTemplate(): ?bool
    {
        return $this->isTemplate;
    }

    public function setIsTemplate(?bool $isTemplate): self
    {
        $this->isTemplate = $isTemplate;

        return $this;
    }

    public function getPreviewUrl(): ?string
    {
        return $this->previewUrl;
    }

    public function setPreviewUrl(?string $previewUrl): self
    {
        $this->previewUrl = $previewUrl;

        return $this;
    }

    public function getDefaultImage(): ?string
    {
        return $this->defaultImage;
    }

    public function setDefaultImage(?string $defaultImage): self
    {
        $this->defaultImage = $defaultImage;

        return $this;
    }

    public function getDefaultLanguage(): ?string
    {
        return $this->defaultLanguage;
    }

    public function setDefaultLanguage(?string $defaultLanguage): self
    {
        $this->defaultLanguage = $defaultLanguage;

        return $this;
    }

    public function getHost(): ?string
    {
        return $this->host;
    }

    public function setHost(string $host): self
    {
        $this->host = $host;

        return $this;
    }

    public function getCategory(): ?string
    {
        return $this->category;
    }

    public function setCategory(string $category): self
    {
        $this->category = $category;

        return $this;
    }

    public function getTemplate(): ?string
    {
        return $this->template;
    }

    public function setTemplate(string $template): self
    {
        $this->template = $template;

        return $this;
    }

    public function getFacebook(): ?string
    {
        return $this->facebook;
    }

    public function setFacebook(string $facebook): self
    {
        $this->facebook = $facebook;

        return $this;
    }

    public function getInstagram(): ?string
    {
        return $this->instagram;
    }

    public function setInstagram(string $instagram): self
    {
        $this->instagram = $instagram;

        return $this;
    }

    public function getLinkedIn(): ?string
    {
        return $this->linkedIn;
    }

    public function setLinkedIn(string $linkedIn): self
    {
        $this->linkedIn = $linkedIn;

        return $this;
    }

    public function getTwitter(): ?string
    {
        return $this->twitter;
    }

    public function setTwitter(string $twitter): self
    {
        $this->twitter = $twitter;

        return $this;
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

    public function getUserCustomer(): ?UserCustomer
    {
        return $this->userCustomer;
    }

    public function setUserCustomer(?UserCustomer $userCustomer): self
    {
        $this->userCustomer = $userCustomer;

        return $this;
    }

    /**
     * @return Collection|Page[]
     */
    public function getPages(): Collection
    {
        return $this->pages;
    }

    public function addPage(Page $page): self
    {
        if (!$this->pages->contains($page)) {
            $this->pages[] = $page;
            $page->setSite($this);
        }

        return $this;
    }

    public function removePage(Page $page): self
    {
        if ($this->pages->contains($page)) {
            $this->pages->removeElement($page);
            // set the owning side to null (unless already changed)
            if ($page->getSite() === $this) {
                $page->setSite(null);
            }
        }

        return $this;
    }

    /**
     * @return array
     */
    public function getSupportedLanguages(): ?array
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

    public function getDomain(): ?Domain
    {
        return $this->domain;
    }

    public function setDomain(Domain $domain): self
    {
        $this->domain = $domain;

        // set the owning side of the relation if necessary
        if ($domain->getSite() !== $this) {
            $domain->setSite($this);
        }

        return $this;
    }

    public function getWorkingFrom(): ?string
    {
        return $this->workingFrom;
    }

    public function setWorkingFrom(?string $workingFrom): self
    {
        $this->workingFrom = $workingFrom;

        return $this;
    }

    public function getWorkingTo(): ?string
    {
        return $this->workingTo;
    }

    public function setWorkingTo(?string $workingTo): self
    {
        $this->workingTo = $workingTo;

        return $this;
    }

    public function getCustomCss(): ?string
    {
        return $this->customCss;
    }

    public function setCustomCss(?string $customCss): self
    {
        $this->customCss = $customCss;

        return $this;
    }

    public function getCustomHtml(): ?string
    {
        return $this->customHtml;
    }

    public function setCustomHtml(?string $customHtml): self
    {
        $this->customHtml = $customHtml;

        return $this;
    }

    public function getTranslatedTemplateName(): ?array
    {
        return $this->translatedTemplateName;
    }

    public function setTranslatedTemplateName(?array $translatedTemplateName): self
    {
        $this->translatedTemplateName = $translatedTemplateName;

        return $this;
    }

    public function getTranslatedAddress(): ?array
    {
        return $this->translatedAddress;
    }

    public function setTranslatedAddress(?array $translatedAddress): self
    {
        $this->translatedAddress = $translatedAddress;

        return $this;
    }

    public function getTranslatedDescription(): ?array
    {
        return $this->translatedDescription;
    }

    public function setTranslatedDescription(?array $translatedDescription): self
    {
        $this->translatedDescription = $translatedDescription;

        return $this;
    }

    public function getTemplateSystemCode(): ?string
    {
        return $this->templateSystemCode;
    }

    public function setTemplateSystemCode(?string $templateSystemCode): self
    {
        $this->templateSystemCode = $templateSystemCode;

        return $this;
    }
}
