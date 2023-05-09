<?php

namespace App\Controller;

use App\Entity\Book;
use App\Entity\BookRequest;
use App\Entity\BookReview;
use App\Entity\MainBookImage;
use App\Entity\User;
use App\Entity\UserReview;
use App\Factory\BookFactory;
use App\Factory\BookHistoryFactory;
use App\Factory\ReviewFactory;
use App\Model\ReviewModel;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Routing\Annotation\Route;

class BookController extends AbstractController
{

    private EntityManagerInterface $entityManager;
    private $reviewFactory;
    private $reviewModel;
    private $bookHistoryFactory;
    private $bookFactory;


    public function __construct(
        BookHistoryFactory $bookHistoryFactory,
        ReviewModel $reviewModel,
        ReviewFactory $reviewFactory,
        EntityManagerInterface $entityManager,
        BookFactory $bookFactory,
    )
    {
        $this->entityManager = $entityManager;
        $this->reviewFactory = $reviewFactory;
        $this->reviewModel = $reviewModel;
        $this->bookHistoryFactory = $bookHistoryFactory;
        $this->bookFactory = $bookFactory;
    }

    public function getSessionUser(string $identifier)
    {
        return $this->entityManager->getRepository(User::class)->findOneBy(['username' => $identifier]);
    }
    #[Route('/books/add_book', name: 'add_book')]
    public function addBook(Request $request) : Response
    {
        $currentUser = $this->getSessionUser($this->getUser()->getUserIdentifier());
        $content = json_decode($request->getContent(), true);

        $book = $this->bookFactory->createBook($currentUser,$content);

        if(empty($this->entityManager->getRepository(MainBookImage::class)->findOneBy(['isbn' => $content['isbn']])))
        {
            $this->bookFactory->createMainBook($content);
        }

        $this->bookHistoryFactory->createHistory($currentUser, $book, 1, false);

        $this->entityManager->persist($book);
        $this->entityManager->flush();

        return $this->json(['message' => 'User created successfully']);
    }

    #[Route('/books/add_book_manual', name: 'add_book_manual')]
    public function addBookManual(Request $request): JsonResponse
    {
        $currentUser = $this->getSessionUser($this->getUser()->getUserIdentifier());

        $data = $request->request->all();

        $decodedImageData = base64_decode($data['thumbnail']);

        $filename = uniqid() . '.jpg';

        $file = fopen($this->getParameter('book_images_directory') . $filename, 'w');

        fwrite($file, $decodedImageData);

        fclose($file);

        $data['thumbnail'] = $filename;

        if( !empty($data['isbn']) && empty($this->entityManager->getRepository(MainBookImage::class)->findOneBy(['isbn' => $data['isbn']])))
        {
            $this->bookFactory->createMainBook($data);
        }

        $book = $this->bookFactory->createBook($currentUser,$data);

        $this->bookHistoryFactory->createHistory($currentUser, $book, 1, false);

        return new JsonResponse(['message' => 'Book added successfully!']);
    }

    #[Route('/books/{id}/image', name: 'book_image')]
    public function getBookImage(Book $book): Response
    {
        $imagePath = $this->getParameter('book_images_directory') . '/' . $book->getImage();

        $response = new BinaryFileResponse($imagePath);
        $response->headers->set('Content-Type', 'image/png'); // replace with the actual mime type of your image

        return $response;
    }

    #[Route('/books/', name: 'get_books')]
    public function getBooks(Request $request) : Response
    {
        $currentUser = $this->getSessionUser($this->getUser()->getUserIdentifier());

        $searchQuery = $request->query->get('search');

        $books = $this->entityManager->getRepository(Book::class)
            ->searchByTitleAndAuthor($searchQuery, $currentUser->getId());

        $data = [];

        foreach ($books as $book) {
            $thumbnail = $book->getThumbnail();

            if(!str_contains($thumbnail, 'http'))
            {
                $file = fopen($this->getParameter('book_images_directory') . $thumbnail, 'r');

                $thumbnailData = '';
                if ($thumbnail) {
                    $thumbnailData = base64_encode(stream_get_contents($file));
                }
            }
            else
                $thumbnailData = $thumbnail;


            $data[] = [
                'id' => $book->getId(),
                'title' => $book->getTitle(),
                'author' => $book->getAuthor(),
                'thumbnail' => $thumbnailData,
                'description' => $book->getDescription(),
                'status' => $book->isAvailable(),
                'owner' => [
                    'id' => $book->getOwner()->getId(),
                    'username' => $book->getOwner()->getUsername()
                ]
            ];
        }

        return new Response(json_encode($data));
    }

    #[Route('/api/books/{id}/history', name: 'book_history')]
    public function getBookHistory(Book $book): Response
    {
        // Todo: check if owner or admin
        $currentUser = $this->getSessionUser($this->getUser()->getUserIdentifier());


        if ($this->getUser()->getRoles()[0] !== 'ROLE_ADMIN' &&
            $book->getOwner()->getId() != $currentUser->getId()
        ) {
            throw new AccessDeniedHttpException('Action not allowed');
        }

        $data = [
            "title" => $book->getTitle(),
            "img" => $book->getThumbnail()
        ];

        $bookHistory = $book->getBookHistories();

        foreach ($bookHistory as $historyData) {
            $data['history'][] =[
                    'dateCreated' => $historyData->getDateCreated(),
                    'action' => $historyData->getAction(),
                    'performedBy' => $historyData->getPerformedBy()->getUsername(),
            ];
        }

        return new Response(json_encode($data));
    }

    #[Route('/api/books/{id}/', name: 'book_remove')]
    public function removeBook(Book $book): JsonResponse
    {
        $this->entityManager->remove($book);
        $this->entityManager->flush();

        return new JsonResponse(['message' => 'Book removed successfully.'], Response::HTTP_OK);
    }

    #[Route('/api/books/{id}/', name: 'book_update')]
    public function updateBook(Request $request, Book $book): JsonResponse
    {
        // Todo: check if owner by usersession
        $data = json_decode($request->getContent(), true);

        $this->bookFactory->updateBook($data, $book);

        return new JsonResponse(['message' => 'Book updated successfully.'], Response::HTTP_OK);
    }

    #[Route('/books/available_isbn/{isbn}', name: 'book_list_lend')]
    public function getAvailableBooks(string $isbn): JsonResponse
    {
        $currentUser = $this->getSessionUser($this->getUser()->getUserIdentifier());

        if($isbn == 'noIsbn')
            $isbn = null;

        $books = $this->entityManager->getRepository(Book::class)->getAvailableNotOwner($currentUser->getId(), $isbn);

        $data = [];

        foreach ($books as $book) {

            $reviews = $this->entityManager->getRepository(BookReview::class)->findBy(['book' => $book->getId()]);

            $reviewsData = [];

            foreach ($reviews as $review)
            {
                $reviewsData[]= [
                    'rating' => $review->getRating(),
                    'review' => $review->getReview(),
                    'reviewedBy' => $review->getReviewedBy()->getUsername()
                ];
            }

            $data[] = [
                'id' => $book->getId(),
                'rating' => $book->getRating() ?? 0,
                'owner' => [
                    'id' => $book->getOwner()->getId(),
                    'username' => $book->getOwner()->getUsername(),
                    'rating' => $book->getOwner()->getRating()
                ],
                'title' => $book->getTitle(),
                'author' => $book->getAuthor(),
                'thumbnail' => $book->getThumbnail(),
                'description' => $book->getDescription(),
                'reviews' => $reviewsData
            ];
        }

        return new JsonResponse($data);
    }

    #[Route('/books/available', name: 'get_all_books')]
    public function getMainAvailableBooks(Request $request): JsonResponse
    {
        $searchQuery = $request->query->get('search');

//        $mainBooks = $this->entityManager->getRepository(MainBookImage::class)->findAll();

        if ($searchQuery) {
            $mainBooks = $this->entityManager->getRepository(MainBookImage::class)->findMainByTitleOrAuthor($searchQuery);
        } else {
            $mainBooks = $this->entityManager->getRepository(MainBookImage::class)->findAll();
        }

        $data = [];

        foreach ($mainBooks as $mainBook)
        {
            $data[] = [
                'isbn' => $mainBook->getIsbn(),
                'title' => $mainBook->getTitle(),
                'author' => $mainBook->getAuthor(),
                'thumbnail' => $mainBook->getImage()
            ];
        }

        return new JsonResponse($data);
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

        // Todo: Book requested
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
            // Convert the return date string to a DateTime object
            $returnDate = new \DateTime($request->getReturnDate());

            // Calculate the difference between the return date and the current date
            $diff = $returnDate->diff($now);

            // Check if the difference is less than or equal to 1 day
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

        // Todo: book request accepted
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

    #[Route('/books/lent', name: 'book_lent')]
    public function getLentBooks(): JsonResponse
    {
        $currentUser = $this->getSessionUser($this->getUser()->getUserIdentifier());
        $requests = $this->entityManager->getRepository(BookRequest::class)->findBy(['requestedBy' => $currentUser->getId(), 'isReturn' => 0,]);

        $data = [];

        foreach ($requests as $request){
            $book = $request->getBook();
            $data[] = [
                'id' => $request->getId(),
                'title' => $book->getTitle(),
                'author' => $book->getAuthor(),
                'thumbnail' => $book->getThumbnail(),
                'owner' => $book->getOwner()->getUsername()
            ];
        }

        return new JsonResponse($data);
    }

    #[Route('/books/lent/{id}/return', name: 'book_lent_return')]
    public function returnBook(Request $request, BookRequest $bookRequest): JsonResponse
    {
        $currentUser = $this->getSessionUser($this->getUser()->getUserIdentifier());
        $data = json_decode($request->getContent(), true);
        $bookRequest->getBook()->setLendedTo(null);
        $bookRequest->setIsLent(0);

        $reviews = $this->entityManager->getRepository(UserReview::class)->findBy(['user' => $bookRequest->getBook()->getOwner()->getId()]);

        $bookRequest->getBook()->getOwner()->setRating($this->reviewModel->calculateRating($data, $reviews, 'lenderRating'));

        $bookReviews = $this->entityManager->getRepository(BookReview::class)->findBy(['book' => $bookRequest->getBook()]);

        $bookRequest->getBook()->setRating($this->reviewModel->calculateRating($data, $bookReviews, 'bookRating'));

        $userReview = new UserReview();

        $this->reviewFactory->createReview($bookRequest, $userReview, $currentUser, $data);

        $bookReview = new BookReview();

        $this->reviewFactory->createReview($bookRequest, $bookReview, $currentUser, $data);

        $bookRequest->setIsReturn(1);

        $this->bookHistoryFactory->createHistory($currentUser, $bookRequest->getBook(), 5, false);

        $this->entityManager->flush();

        return new JsonResponse(['message' => 'Book request accepted.'], Response::HTTP_OK);
    }

    #[Route('/books/returned', name: 'book_returned')]
    public function getReturnedBooks(EntityManagerInterface $entityManager): JsonResponse
    {
        $currentUser = $this->getSessionUser($this->getUser()->getUserIdentifier());

        $requests = $entityManager->getRepository(BookRequest::class)->findBy(['isReturn' => 1]);

        foreach($requests as $key => $request)
        {
            if($request->getBook()->getOwner()->getId() != $currentUser->getId())
                unset($requests[$key]);
        }

        $data = [];

        foreach ($requests as $request){
            $book = $request->getBook();
            $data[] = [
                'id' => $request->getId(),
                'bookId' =>$request->getBook()->getId(),
                'ownerId' => $book->getOwner()->getId(),
                'title' => $book->getTitle(),
                'author' => $book->getAuthor(),
                'thumbnail' => $book->getThumbnail(),
                'lendee' => [
                    'username' => $request->getRequestedBy()->getUsername(),
                    'id' => $request->getRequestedBy()->getId()
                ]
            ];
        }

        return new JsonResponse($data);
    }

    #[Route('/books/my_lent', name: 'my_lent')]
    public function getMyLentBooks(EntityManagerInterface $entityManager): JsonResponse
    {
        $currentUser = $this->getSessionUser($this->getUser()->getUserIdentifier());

        $requests = $entityManager->getRepository(BookRequest::class)->getMyLentBooks($currentUser->getId());

        $data = [];

        foreach ($requests as $request)
        {
            $book = $request->getBook();
            $data[] = [
                'book' => [
                    'title' => $book->getTitle(),
                    'author' => $book->getAuthor(),
                    'thumbnail' => $book->getThumbnail(),
                    'description' => $book->getDescription(),
                ],
                'requestDate' => $request->getRequestDate(),
                'returnDate' => $request->getReturnDate(),
                'requestedBy' => [
                    'id' => $request->getRequestedBy()->getId(),
                    'username' => $request->getRequestedBy()->getUsername(),
                    'rating' => $request->getRequestedBy()->getRating()
                ]
            ];
        }

        return new JsonResponse($data);
    }

    #[Route('/requests/pending', name: 'pending_requests')]
    public function getMyRequestsBooks(EntityManagerInterface $entityManager): JsonResponse
    {
        $currentUser = $this->getSessionUser($this->getUser()->getUserIdentifier());

        $requests = $entityManager->getRepository(BookRequest::class)->findBy(['isActive' => 1, 'requestedBy' => $currentUser->getId()]);

        $data = [];

        foreach ($requests as $request)
        {
            $book = $request->getBook();
            $owner = $book->getOwner();
            $data[] = [
                'id' => $request->getId(),
                'book' => [
                    'title' => $book->getTitle(),
                    'author' => $book->getAuthor(),
                    'thumbnail' => $book->getThumbnail(),
                    'description' => $book->getDescription(),
                ],
                'requestDate' => $request->getRequestDate(),
                'returnDate' => $request->getReturnDate(),
                'owner' => [
                    'id' => $owner->getId(),
                    'username' => $owner->getUsername(),
                    'rating' => $owner->getRating(),
                    'phone' => $owner->getNumber(),
                ]
            ];
        }

        return new JsonResponse($data);
    }

    #[Route('/request/pending/cancel/{id}', name: 'cancel_request')]
    public function cancelRequest(EntityManagerInterface $entityManager, BookRequest $bookRequest): JsonResponse
    {
        $currentUser = $this->getSessionUser($this->getUser()->getUserIdentifier());

        // Todo: if owner

        $entityManager->getRepository(BookRequest::class)->remove($bookRequest);
        $entityManager->flush();

        return new JsonResponse(['message' => 'Book request accepted.'], Response::HTTP_OK);
    }

    /**
     * @Route("/books/lent/{id_book}/{id_user}/{id_request}/accept")
     *
     * @ParamConverter("book", options={"mapping": {"id_book" : "id"}})
     * @ParamConverter("user", options={"mapping": {"id_user" : "id"}})
     * @ParamConverter("bookRequest", options={"mapping": {"id_request" : "id"}})
     */
    public function acceptReturnedBook(Request $request, Book $book, User $user, BookRequest $bookRequest, EntityManagerInterface $entityManager): JsonResponse
    {
        $currentUser = $this->getSessionUser($this->getUser()->getUserIdentifier());
        $data = json_decode($request->getContent(), true);
        $reviews = $entityManager->getRepository(UserReview::class)->findBy(['user' => $user->getId()]);

        $user->setRating($this->reviewModel->calculateRating($data, $reviews, 'lenderRating'));

        $this->reviewFactory->returnBookReview($user, $data, $currentUser);

        $book->setAvailable(1);
        $bookRequest->setIsReturn(0);

        // Todo: Return Accepted
        $this->bookHistoryFactory->createHistory($currentUser, $bookRequest->getBook(), 6, false);

        $this->entityManager->flush();

        return new JsonResponse(['message' => 'Book request accepted.'], Response::HTTP_OK);
    }
}
