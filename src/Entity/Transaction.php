<?php

namespace App\Entity;

use App\Repository\Billing\TransactionRepository;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;

/**
 * @ORM\Entity(repositoryClass=TransactionRepository::class)
 */
class Transaction
{
    public const STATUS_PENDING = "pending",
        STATUS_COMPLETED = "completed",
        STATUS_FAILED = "failed";

    use TimestampableEntity;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $subscription;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $status;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $invoicePdf;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSubscription(): ?string
    {
        return $this->subscription;
    }

    public function setSubscription(string $subscription): self
    {
        $this->subscription = $subscription;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getInvoicePdf(): ?string
    {
        return $this->invoicePdf;
    }

    public function setInvoicePdf(?string $invoicePdf): self
    {
        $this->invoicePdf = $invoicePdf;

        return $this;
    }
}
