<?php

namespace App\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @MongoDB\Document
 */
class User implements UserInterface
{
    use TimestampableEntity;

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


    /**
     * @var string $password
     * @MongoDB\Field(type="string")
     */
    private $plainPassword;

    /**
     * @var bool $active
     * @MongoDB\Field(type="boolean")
     */
    private $active;

    /**
     * @var array $sites
     *
     * @MongoDB\ReferenceMany(targetDocument="Site", mappedBy="user", storeAs="id")
     */
    protected $sites = [];

    /**
     * @var array $files
     *
     * @MongoDB\ReferenceMany(targetDocument="File", mappedBy="user", storeAs="id")
     */
    protected $files = [];

    protected string $cloudflareApiKey;

    protected string $ip;

    protected string $cloudflareUserKey;

    protected string $cloudflarePassword;

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

    public function getRoles(): array
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

    public function getUsername(): string
    {
        return $this->email;
    }

    public function eraseCredentials(): void
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

    /**
     * @return string
     */
    public function getCloudflareApiKey(): string
    {
        return $this->cloudflareApiKey;
    }

    public function setCloudflareApiKey(string $cloudflareApiKey): void
    {
        $this->cloudflareApiKey = $cloudflareApiKey;
    }

    public function getIp(): string
    {
        return $this->ip;
    }

    public function setIp(string $ip): void
    {
        $this->ip = $ip;
    }

    public function getCloudflareUserKey(): string
    {
        return $this->cloudflareUserKey;
    }

    public function setCloudflareUserKey(string $cloudflareUserKey): void
    {
        $this->cloudflareUserKey = $cloudflareUserKey;
    }

    public function getCloudflarePassword(): string
    {
        return $this->cloudflarePassword;
    }

    public function setCloudflarePassword(string $cloudflarePassword): void
    {
        $this->cloudflarePassword = $cloudflarePassword;
    }
}
