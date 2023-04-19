<?php

namespace App\Entity;

use App\Repository\BookRequestRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BookRequestRepository::class)]
class BookRequest
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'bookRequests')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Book $book = null;

    #[ORM\Column(length: 255)]
    private ?string $requestDate = null;

    #[ORM\Column(length: 255)]
    private ?string $returnDate = null;

    #[ORM\Column]
    private ?int $isActive = null;

    #[ORM\Column]
    private ?int $isLent = null;

    #[ORM\ManyToOne(inversedBy: 'bookRequests')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $requestedBy = null;

    #[ORM\Column]
    private ?int $isReturn = null;

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

    public function getRequestDate(): ?string
    {
        return $this->requestDate;
    }

    public function setRequestDate(string $requestDate): self
    {
        $this->requestDate = $requestDate;

        return $this;
    }

    public function getReturnDate(): ?string
    {
        return $this->returnDate;
    }

    public function setReturnDate(string $returnDate): self
    {
        $this->returnDate = $returnDate;

        return $this;
    }

    public function getIsActive(): ?int
    {
        return $this->isActive;
    }

    public function setIsActive(int $isActive): self
    {
        $this->isActive = $isActive;

        return $this;
    }

    public function getIsLent(): ?int
    {
        return $this->isLent;
    }

    public function setIsLent(int $isLent): self
    {
        $this->isLent = $isLent;

        return $this;
    }

    public function getRequestedBy(): ?User
    {
        return $this->requestedBy;
    }

    public function setRequestedBy(?User $requestedBy): self
    {
        $this->requestedBy = $requestedBy;

        return $this;
    }

    public function getIsReturn(): ?int
    {
        return $this->isReturn;
    }

    public function setIsReturn(int $isReturn): self
    {
        $this->isReturn = $isReturn;

        return $this;
    }
}
