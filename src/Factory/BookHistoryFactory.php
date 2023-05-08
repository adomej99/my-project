<?php

namespace App\Factory;

use App\Entity\Book;
use App\Entity\BookHistory;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class BookHistoryFactory
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    public function createHistory(User $performedBy, Book $book, int $action, bool $isRequest): void
    {
        $bookHistory = new BookHistory();

        $bookHistory->setBook($book);
        $bookHistory->setDateCreated(date("Y-m-d h:i:sa"));
        // Todo: Return Requested
        $bookHistory->setAction($action);
        $bookHistory->setIsRequest($isRequest);
        $bookHistory->setPerformedBy($performedBy);

        $this->entityManager->persist($bookHistory);
        $this->entityManager->flush();
    }
}
