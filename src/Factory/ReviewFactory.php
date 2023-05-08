<?php

namespace App\Factory;

use App\Entity\BookRequest;
use App\Entity\BookReview;
use App\Entity\User;
use App\Entity\UserReview;
use Doctrine\ORM\EntityManagerInterface;

class ReviewFactory
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    public function createReview(
        BookRequest $bookRequest,
        ReviewFactoryInterface $review,
        User $currentUser,
        array $data
    )
    {
        $review->setReviewedBy($currentUser);

        if ($review instanceof BookReview) {
            $review->setRating((int)$data['bookRating']);
            $review->setReview($data['bookReview']);
            $review->setBook($bookRequest->getBook());
        } elseif ($review instanceof UserReview) {
            $review->setRating((int)$data['lenderRating']);
            $review->setReview($data['lenderReview']);
            $review->setUser($bookRequest->getBook()->getOwner());
        }

        $this->entityManager->persist($review);
        $this->entityManager->flush();
    }

    public function returnBookReview(User $reviewedUser, array $data, User $currentUser)
    {
        $userReview = new UserReview();

        $userReview->setUser($reviewedUser);
        $userReview->setReview($data['lenderReview']);
        $userReview->setRating((int)$data['lenderRating']);
        $userReview->setReviewedBy($currentUser);

        $this->entityManager->persist($userReview);
        $this->entityManager->flush();
    }

}
