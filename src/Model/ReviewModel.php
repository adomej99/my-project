<?php

namespace App\Model;

class ReviewModel
{
    public function calculateRating(array $data, array $reviews, string $ratingType): float|int
    {
        $rating = 0;

        if(count($reviews) != 0)
        {
            foreach ($reviews as $review)
                $rating = $rating + $review->getRating();

            $rating = ($rating + (int)$data[$ratingType]) / (count($reviews) + 1);
        }
        else
            $rating = $rating + (int)$data[$ratingType];

        return $rating;
    }
}
