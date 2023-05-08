<?php

namespace App\Factory;

use App\Entity\User;
use Symfony\Component\Security\Core\User\UserInterface;

interface ReviewFactoryInterface
{
    public function setReviewedBy(UserInterface $reviewedBy): self;

    public function setReview(string $review): self;

    public function setRating(int $rating): self;
}
