<?php

namespace App\Factory;

use App\Entity\Book;
use App\Entity\MainBookImage;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class BookFactory
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function createBook(User $currentUser, array $content): Book
    {
        $book = new Book();

        $book->setTitle($content['title']);
        $book->setAuthor($content['author']);
        $book->setThumbnail($content['thumbnail']);
        $book->setDescription(substr($content['description'], 0, 250));
        $book->setOwner($currentUser);
        $book->setIsbn($content['isbn']);

        $this->entityManager->persist($book);
        $this->entityManager->flush();

        return $book;
    }

    public function createMainBook(array $content): void
    {
        $mainBookImage = new MainBookImage();

        $mainBookImage->setIsbn($content['isbn']);
        $mainBookImage->setImage($content['thumbnail']);
        $mainBookImage->setTitle($content['title']);
        $mainBookImage->setAuthor($content['author']);

        $this->entityManager->persist($mainBookImage);
        $this->entityManager->flush();
    }

    public function updateBook(array $data, Book $book)
    {
        $book->setTitle($data['title'] ?? $book->getTitle());
        $book->setAuthor($data['author'] ?? $book->getAuthor());
        $book->setThumbnail($data['thumbnail'] ?? $book->getThumbnail());
        $book->setDescription($data['description'] ?? $book->getDescription());
        $book->setAvailable(!$data['status']);

        $this->entityManager->flush();
    }


}
