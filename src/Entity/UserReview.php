<?php

namespace App\Entity;

use App\Factory\ReviewFactoryInterface;
use App\Repository\UserReviewRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserReviewRepository::class)]
class UserReview implements ReviewFactoryInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $review = null;

    #[ORM\Column]
    private ?int $rating = null;

    #[ORM\ManyToOne(inversedBy: 'userReviews')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $reviewedBy = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getReview(): ?string
    {
        return $this->review;
    }

    public function setReview(?string $review): self
    {
        $this->review = $review;

        return $this;
    }

    public function getRating(): ?int
    {
        return $this->rating;
    }

    public function setRating(int $rating): self
    {
        $this->rating = $rating;

        return $this;
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

    public function getReviewedBy(): ?User
    {
        return $this->reviewedBy;
    }

    public function setReviewedBy(?UserInterface $reviewedBy): self
    {
        $this->reviewedBy = $reviewedBy;

        return $this;
    }
}
