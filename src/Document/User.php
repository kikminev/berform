<?php

namespace App\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Timestampable\Timestampable;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @MongoDB\Document
 */
class User implements UserInterface
{
    /**
     * @MongoDB\Id
     */
    private $id;

    /**
     * @var string $email
     * @MongoDB\Field(type="string")
     */
    private $email;

    /**
     * @var string $password
     * @MongoDB\Field(type="string")
     */
    private $password;

    private $plainPassword;

    /**
     * @var bool $active
     * @MongoDB\Field(type="boolean")
     */
    private $active;

    /**
     * @var array $sites
     *
     * @MongoDB\ReferenceMany(targetDocument="Site", mappedBy="user")
     */
    protected $sites = array();

    /**
     * @var array $files
     *
     * @MongoDB\ReferenceMany(targetDocument="File", mappedBy="user")
     */
    protected $files = array();

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
     * @return string
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @param string $password
     */
    public function setPassword(string $password): void
    {
        $this->password = $password;
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

    public function getRoles()
    {
        //$roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function getSalt()
    {
        // TODO: Implement getSalt() method.
    }

    public function getUsername()
    {
        return $this->email;
    }

    public function eraseCredentials()
    {
        // TODO: Implement eraseCredentials() method.
    }

    /**
     * @return string
     */
    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    /**
     * @param string $plainPassword
     */
    public function setPlainPassword(string $plainPassword): void
    {
        $this->plainPassword = $plainPassword;
    }

    /**
     * @return array
     */
    public function getSites(): array
    {
        return $this->sites;
    }

    /**
     * @param array $sites
     */
    public function setSites(array $sites): void
    {
        $this->sites = $sites;
    }

    /**
     * @return array
     */
    public function getFiles(): array
    {
        return $this->files;
    }

    /**
     * @param array $files
     */
    public function setFiles(array $files): void
    {
        $this->files = $files;
    }
}
