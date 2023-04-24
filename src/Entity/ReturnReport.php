<?php

namespace App\Entity;

use App\Repository\ReturnReportRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ReturnReportRepository::class)]
class ReturnReport
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $returnedBy = null;

    #[ORM\Column(length: 255)]
    private ?string $report = null;

    #[ORM\Column(length: 255)]
    private ?string $date = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?BookRequest $request = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getReturnedBy(): ?User
    {
        return $this->returnedBy;
    }

    public function setReturnedBy(?User $returnedBy): self
    {
        $this->returnedBy = $returnedBy;

        return $this;
    }

    public function getReport(): ?string
    {
        return $this->report;
    }

    public function setReport(string $report): self
    {
        $this->report = $report;

        return $this;
    }

    public function getDate(): ?string
    {
        return $this->date;
    }

    public function setDate(string $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getRequest(): ?BookRequest
    {
        return $this->request;
    }

    public function setRequest(BookRequest $request): self
    {
        $this->request = $request;

        return $this;
    }
}
