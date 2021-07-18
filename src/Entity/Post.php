<?php

namespace App\Entity;

use App\Repository\PostRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;

/**
 * @ORM\Entity(repositoryClass=PostRepository::class)
 */
class Post
{
    use TimestampableEntity;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Site::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $site;

    /**
     * @ORM\ManyToOne(targetEntity=UserCustomer::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $userCustomer;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $slug;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $isActive;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $isDeleted;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $featuredParallax;

    /**
     * @ORM\Column(type="json", nullable=true)
     */
    private $translatedTitle = [];

    /**
     * @ORM\Column(type="json", nullable=true)
     */
    private $translatedExcerpt = [];

    /**
     * @ORM\Column(type="json", nullable=true)
     */
    private $translatedContent = [];

    /**
     * @ORM\Column(type="json", nullable=true)
     */
    private $translatedKeywords = [];

    /**
     * @ORM\Column(type="json", nullable=true)
     */
    private $translatedMetaDescription = [];
    
    /**
     * @ORM\ManyToOne(targetEntity=File::class, cascade={"persist", "remove"})
     */
    private $defaultImage;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $publishedAt;

    /**
     * @ORM\OneToMany(targetEntity=File::class, mappedBy="post")
     */
    private $files;

    public function __construct()
    {
        $this->files = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSite(): ?Site
    {
        return $this->site;
    }

    public function setSite(?Site $site): self
    {
        $this->site = $site;

        return $this;
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

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getIsActive(): ?bool
    {
        return $this->isActive;
    }

    public function setIsActive(?bool $isActive): self
    {
        $this->isActive = $isActive;

        return $this;
    }

    public function getIsDeleted(): ?bool
    {
        return $this->isDeleted;
    }

    public function setIsDeleted(?bool $isDeleted): self
    {
        $this->isDeleted = $isDeleted;

        return $this;
    }

    public function getTranslatedTitle(): ?array
    {
        return $this->translatedTitle;
    }

    public function setTranslatedTitle(?array $translatedTitle): self
    {
        $this->translatedTitle = $translatedTitle;

        return $this;
    }

    public function getTranslatedExcerpt(): ?array
    {
        return $this->translatedExcerpt;
    }

    public function setTranslatedExcerpt(?array $translatedExcerpt): self
    {
        $this->translatedExcerpt = $translatedExcerpt;

        return $this;
    }

    public function getTranslatedContent(): ?array
    {
        return $this->translatedContent;
    }

    public function setTranslatedContent(?array $translatedContent): self
    {
        $this->translatedContent = $translatedContent;

        return $this;
    }

    public function getTranslatedKeywords(): ?array
    {
        return $this->translatedKeywords;
    }

    public function setTranslatedKeywords(?array $translatedKeywords): self
    {
        $this->translatedKeywords = $translatedKeywords;

        return $this;
    }

    public function getTranslatedMetaDescription(): ?array
    {
        return $this->translatedMetaDescription;
    }

    public function setTranslatedMetaDescription(?array $translatedMetaDescription): self
    {
        $this->translatedMetaDescription = $translatedMetaDescription;

        return $this;
    }


    public function getDefaultImage(): ?File
    {
        return $this->defaultImage;
    }

    public function setDefaultImage(?File $defaultImage): self
    {
        $this->defaultImage = $defaultImage;

        return $this;
    }

    public function getFeaturedParallax(): ?bool
    {
        return $this->featuredParallax;
    }

    public function setFeaturedParallax(?bool $featuredParallax): self
    {
        $this->featuredParallax = $featuredParallax;

        return $this;
    }

    public function getPublishedAt(): ?\DateTimeInterface
    {
        return $this->publishedAt;
    }

    public function setPublishedAt(?\DateTimeInterface $publishedAt): self
    {
        $this->publishedAt = $publishedAt;

        return $this;
    }

    /**
     * @return Collection|File[]
     */
    public function getFiles(): Collection
    {
        return $this->files;
    }

    public function addFile(File $file): self
    {
        if (!$this->files->contains($file)) {
            $this->files[] = $file;
            $file->setPost($this);
        }

        return $this;
    }

    public function removeFile(File $file): self
    {
        if ($this->files->removeElement($file)) {
            // set the owning side to null (unless already changed)
            if ($file->getPost() === $this) {
                $file->setPost(null);
            }
        }

        return $this;
    }
}
