<?php

namespace App\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @MongoDB\Document
 */
class Message
{
    use TimestampableEntity;

    public function __clone()
    {
        $this->id = null;
    }

    /**
     * @MongoDB\Id
     */
    protected $id;

    /**
     * @var null|string $firstName
     * @MongoDB\Field(type="string")
     * @Assert\NotBlank()
     */
    protected $firstName;

    /**
     * @var null|string $lastName
     * @MongoDB\Field(type="string")
     * @Assert\NotBlank()
     */
    protected $lastName;

    /**
     * @var null|string $message
     * @MongoDB\Field(type="string")
     * @Assert\NotBlank()
     */
    protected $message;

    /**
     * @var null|string $phone
     * @MongoDB\Field(type="string")
     * @Assert\NotBlank()
     */
    protected $phone;

    /**
     * @var null|string $email
     * @MongoDB\Field(type="string")
     * @Assert\NotBlank()
     */
    protected $email;

    /**
     * @MongoDB\ReferenceOne(targetDocument="Site", storeAs="id")
     */
    protected $site;

    /**
     * @var User $user
     * @MongoDB\ReferenceOne(targetDocument="User", storeAs="id")
     */
    protected $user;

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
    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    /**
     * @param null|string $firstName
     */
    public function setFirstName(?string $firstName): void
    {
        $this->firstName = $firstName;
    }

    /**
     * @return null|string
     */
    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    /**
     * @param null|string $lastName
     */
    public function setLastName(?string $lastName): void
    {
        $this->lastName = $lastName;
    }

    /**
     * @return null|string
     */
    public function getMessage(): ?string
    {
        return $this->message;
    }

    /**
     * @param null|string $message
     */
    public function setMessage(?string $message): void
    {
        $this->message = $message;
    }

    /**
     * @return null|string
     */
    public function getPhone(): ?string
    {
        return $this->phone;
    }

    /**
     * @param null|string $phone
     */
    public function setPhone(?string $phone): void
    {
        $this->phone = $phone;
    }

    /**
     * @return null|string
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * @param null|string $email
     */
    public function setEmail(?string $email): void
    {
        $this->email = $email;
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
}
