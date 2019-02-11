<?php

namespace App\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @MongoDB\Document
 */
class Site
{
    /**
     * @MongoDB\Id
     */
    protected $id;

    /**
     * @var User $user
     * @MongoDB\ReferenceOne(targetDocument="User", inversedBy="sites")
     */
    protected $user;

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
    protected $instagram;

    /**
     * @MongoDB\Field(type="string")
     */
    protected $twitter;

    /**
     * @var null|string $address
     * @MongoDB\Field(type="string")
     */
    protected $address;

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
     * @var bool $isTemplate
     * @MongoDB\Field(type="bool")
     */
    protected $isTemplate;

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
     */
    protected $host;

    /**
     * @var \DateTime $updatedAt
     *
     * @MongoDB\Date
     * @Gedmo\Timestampable(on="update")
     */
    protected $updatedAt;

    /**
     * @var \DateTime $createdAt
     *
     * @MongoDB\Date
     * @Gedmo\Timestampable(on="create")
     */
    protected $createdAt;

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
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * @param \DateTime $updatedAt
     */
    public function setUpdatedAt($updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param \DateTime $createdAt
     */
    public function setCreatedAt($createdAt): void
    {
        $this->createdAt = $createdAt;
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
     * @return null|string
     */
    public function getAddress(): ?string
    {
        return $this->address;
    }

    /**
     * @param null|string $address
     */
    public function setAddress(?string $address): void
    {
        $this->address = $address;
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


}
