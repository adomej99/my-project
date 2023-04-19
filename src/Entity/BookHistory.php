<?php

namespace App\Entity;

use App\Repository\BookHistoryRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BookHistoryRepository::class)]
class BookHistory
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'bookHistories')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Book $book = null;

    #[ORM\Column(length: 255)]
    private ?string $dateCreated = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $performedBy = null;

    #[ORM\Column]
    private ?int $action = null;

    #[ORM\Column]
    private ?bool $isRequest = null;

    #[ORM\Column(nullable: true)]
    private ?int $currentlyActive = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBook(): ?Book
    {
        return $this->book;
    }

    public function setBook(?Book $book): self
    {
        $this->book = $book;

        return $this;
    }

    public function getDateCreated(): ?string
    {
        return $this->dateCreated;
    }

    public function setDateCreated(string $dateCreated): self
    {
        $this->dateCreated = $dateCreated;

        return $this;
    }

    public function getPerformedBy(): ?User
    {
        return $this->performedBy;
    }

    public function setPerformedBy(?User $performedBy): self
    {
        $this->performedBy = $performedBy;

        return $this;
    }

    public function getAction(): ?int
    {
        return $this->action;
    }

    public function setAction(int $action): self
    {
        $this->action = $action;

        return $this;
    }

    public function isIsRequest(): ?bool
    {
        return $this->isRequest;
    }

    public function setIsRequest(bool $isRequest): self
    {
        $this->isRequest = $isRequest;

        return $this;
    }

    public function getCurrentlyActive(): ?int
    {
        return $this->currentlyActive;
    }

    public function setCurrentlyActive(?int $currentlyActive): self
    {
        $this->currentlyActive = $currentlyActive;

        return $this;
    }
}
