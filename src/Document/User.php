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
     */
    private $plainPassword;

    /**
     * @var bool $active
     * @MongoDB\Field(type="boolean")
     */
    private $active;

    /**
     * @var null|bool $system
     * @MongoDB\Field(type="boolean")
     */
    private $system;

    /**
     * @var array $sites
     *
     * @MongoDB\ReferenceMany(targetDocument="Site", mappedBy="user", storeAs="id")
     */
    private $sites = [];

    /**
     * @var array $files
     *
     * @MongoDB\ReferenceMany(targetDocument="File", mappedBy="user", storeAs="id")
     */
    private $files = [];

    /**
     * @var array $roles
     * @MongoDB\Field(type="hash")
     */
    private $roles = [];

    /**
     * @var string $cloudflareApiKey
     * @MongoDB\Field(type="string")
     */
    private string $cloudflareApiKey = '';

    /**
     * @var string $ip
     * @MongoDB\Field(type="string")
     */
    private string $ip = '';

    /**
     * @var string $cloudflareUserKey
     * @MongoDB\Field(type="string")
     */
    private string $cloudflareUserKey = '';

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
        $roles = $this->roles;
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @param array $roles
     */
    public function setRoles(array $roles): void
    {
        $this->roles = $roles;
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

    /**
     * @param string $cloudflareApiKey
     */
    public function setCloudflareApiKey(string $cloudflareApiKey): void
    {
        $this->cloudflareApiKey = $cloudflareApiKey;
    }

    /**
     * @return string
     */
    public function getIp(): string
    {
        return $this->ip;
    }

    /**
     * @param string $ip
     */
    public function setIp(string $ip): void
    {
        $this->ip = $ip;
    }

    /**
     * @return string
     */
    public function getCloudflareUserKey(): string
    {
        return $this->cloudflareUserKey;
    }

    /**
     * @param string $cloudflareUserKey
     */
    public function setCloudflareUserKey(string $cloudflareUserKey): void
    {
        $this->cloudflareUserKey = $cloudflareUserKey;
    }

    /**
     * @return bool|null
     */
    public function getSystem(): ?bool
    {
        return $this->system;
    }

    /**
     * @param bool|null $system
     */
    public function setSystem(?bool $system): void
    {
        $this->system = $system;
    }
}
