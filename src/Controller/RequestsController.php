<?php

namespace App\Controller;

use App\Entity\Book;
use App\Entity\BookRequest;
use App\Entity\User;
use App\Factory\BookHistoryFactory;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RequestsController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private BookHistoryFactory $bookHistoryFactory;

    public function __construct(
        BookHistoryFactory $bookHistoryFactory,
        EntityManagerInterface $entityManager,
    )
    {
        $this->entityManager = $entityManager;
        $this->bookHistoryFactory = $bookHistoryFactory;
    }

    public function getSessionUser(string $identifier)
    {
        return $this->entityManager->getRepository(User::class)->findOneBy(['username' => $identifier]);
    }

    #[Route('/books/available/{id}', name: 'book_lend_request')]
    public function requestBook(Request $request, Book $book): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $bookRequest = new BookRequest();
        $currentUser = $this->getSessionUser($this->getUser()->getUserIdentifier());

        $bookRequest->setBook($book);
        $bookRequest->setRequestDate(date("Y-m-d h:i:sa"));
        $bookRequest->setReturnDate($data['date']);
        $bookRequest->setIsActive(1);
        $bookRequest->setIsLent(0);
        $bookRequest->setIsReturn(0);
        $bookRequest->setRequestedBy($currentUser);

        $this->bookHistoryFactory->createHistory($currentUser, $bookRequest->getBook(), 2, false);

        $this->entityManager->persist($bookRequest);
        $this->entityManager->flush();

        return new JsonResponse(['message' => 'Book updated successfully.'], Response::HTTP_OK);
    }

    #[Route('/books/requests/', name: 'book_request_list')]
    public function getBookRequests(): JsonResponse
    {
        $data = [];

        $currentUser = $this->getSessionUser($this->getUser()->getUserIdentifier());
        $bookRequests = $this->entityManager->getRepository(BookRequest::class)->getBookRequests($currentUser->getId());

        foreach ($bookRequests as $bookRequest) {
            $book = $bookRequest->getBook();
            $data[] = [
                'id' => $bookRequest->getId(),
                'book' => [
                    'id' => $book->getId(),
                    'title' => $book->getTitle(),
                    'author' => $book->getAuthor(),
                    'thumbnail' => $book->getThumbnail(),
                    'description' => $book->getDescription(),
                    'status' => $book->isAvailable(),
                ],
                'requestDate' => $bookRequest->getRequestDate(),
                'returnDate' => $bookRequest->getReturnDate(),
                'requestedBy' => [
                    'id' => $bookRequest->getRequestedBy()->getId(),
                    'username' => $bookRequest->getRequestedBy()->getUsername(),
                    'rating' => $bookRequest->getRequestedBy()->getRating(),
                    'phone' => $bookRequest->getRequestedBy()->getNumber()
                ]
            ];
        }

        return new JsonResponse($data);
    }

    #[Route('/requests/expired', name: 'expired_requests')]
    public function getExpiredRequests(): JsonResponse
    {
        $currentUser = $this->getSessionUser($this->getUser()->getUserIdentifier());
        $requests = $this->entityManager->getRepository(BookRequest::class)->findBy(['requestedBy' => $currentUser->getId(), 'isLent'=> 1]);

        $now = new \DateTime();

        $expiringRequests = [];

        foreach ($requests as $request) {
            $returnDate = new \DateTime($request->getReturnDate());

            $diff = $returnDate->diff($now);

            if ($diff->days <= 1) {
                $expiringRequests[] = [
                    'title' => $request->getBook()->getTitle(),
                    'returnDate' => $request->getReturnDate(),
                ];
            }
        }

        return new JsonResponse($expiringRequests);
    }

    #[Route('/books/requests/{id}/accept', name: 'book_lend_request_accept')]
    public function acceptRequest(BookRequest $bookRequest): JsonResponse
    {
        $currentUser = $this->getSessionUser($this->getUser()->getUserIdentifier());

        $book = $bookRequest->getBook();

        $this->bookHistoryFactory->createHistory($currentUser, $bookRequest->getBook(), 3, false);

        $bookRequest->setIsActive(0);
        $bookRequest->setIsLent(1);
        $book->setAvailable(0);
        $book->setLendedTo($currentUser);

        $this->entityManager->flush();

        return new JsonResponse(['message' => 'Book request accepted.'], Response::HTTP_OK);
    }

    #[Route('/books/requests/{id}/decline', name: 'book_lend_request_decline')]
    public function declineRequest(BookRequest $bookRequest): JsonResponse
    {
        $currentUser = $this->getSessionUser($this->getUser()->getUserIdentifier());

        $this->bookHistoryFactory->createHistory($currentUser, $bookRequest->getBook(), 4, false);

        $this->entityManager->remove($bookRequest);

        return new JsonResponse(['message' => 'Book request accepted.'], Response::HTTP_OK);
    }

    #[Route('/request/pending/cancel/{id}', name: 'cancel_request')]
    public function cancelPendingRequest(EntityManagerInterface $entityManager, BookRequest $bookRequest): JsonResponse
    {
        $entityManager->getRepository(BookRequest::class)->remove($bookRequest);
        $entityManager->flush();

        return new JsonResponse(['message' => 'Book request accepted.'], Response::HTTP_OK);
    }
}
