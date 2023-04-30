<?php

namespace App\Controller;

use App\Entity\Book;
use App\Entity\BookHistory;
use App\Entity\BookRequest;
use App\Entity\BookReview;
use App\Entity\MainBookImage;
use App\Entity\User;
use App\Entity\UserReview;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class BookController extends AbstractController
{

    private EntityManagerInterface $entityManager;
    private TokenStorageInterface $tokenStorage;

    public function __construct(EntityManagerInterface $entityManager, TokenStorageInterface $tokenStorage)
    {
        $this->entityManager = $entityManager;
        $this->tokenStorage = $tokenStorage;
    }

    public function getSessionUser(string $identifier)
    {
        return $this->entityManager->getRepository(User::class)->findOneBy(['username' => $identifier]);
    }
    #[Route('/books/add_book', name: 'add_book')]
    public function addBook(Request $request, EntityManagerInterface $entityManager) : Response
    {
        $currentUser = $this->getSessionUser($this->getUser()->getUserIdentifier());

        $book = new Book();
        $content = json_decode($request->getContent(), true);
        $owner = $entityManager->getRepository(User::class)->find($currentUser->getId());

        $book->setTitle($content['title']);
        $book->setAuthor($content['author']);
        $book->setThumbnail($content['thumbnail']);
        $book->setDescription(substr($content['description'], 0, 250));
        $book->setOwner($owner);
        $book->setIsbn($content['isbn']);

        if(empty($entityManager->getRepository(MainBookImage::class)->findOneBy(['isbn' => $content['isbn']])))
        {
            $mainBookImage = new MainBookImage();

            $mainBookImage->setIsbn($content['isbn']);
            $mainBookImage->setImage($content['thumbnail']);
            $mainBookImage->setTitle($content['title']);
            $mainBookImage->setAuthor($content['author']);

            $entityManager->persist($mainBookImage);
        }


        $bookHistory = new BookHistory();

        $bookHistory->setBook($book);
        $bookHistory->setDateCreated(date("Y-m-d h:i:sa"));
        $bookHistory->setAction(1);
        $bookHistory->setIsRequest(false);
        $bookHistory->setPerformedBy($owner);


        $entityManager->persist($bookHistory);
        $entityManager->persist($book);
        $entityManager->flush();

        return $this->json(['message' => 'User created successfully']);
    }

    #[Route('/books/add_book_manual', name: 'add_book_manual')]
    public function addBookManual(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $currentUser = $this->getSessionUser($this->getUser()->getUserIdentifier());
        $owner = $entityManager->getRepository(User::class)->find($currentUser->getId());

        $data = $request->request->all();

        $book = new Book();
        $book->setTitle($data['title']);
        $book->setAuthor($data['author']);
        $book->setDescription($data['description']);
        $book->setOwner($entityManager->getRepository(User::class)->find($owner->getId()));

        $decodedImageData = base64_decode($data['thumbnail']);

        $filename = uniqid() . '.jpg';

        $file = fopen($this->getParameter('book_images_directory') . $filename, 'w');

        fwrite($file, $decodedImageData);

        fclose($file);

        $book->setThumbnail($filename);

        $entityManager->persist($book);
        $entityManager->flush();

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
    public function getBooks() : Response
    {
        $currentUser = $this->getSessionUser($this->getUser()->getUserIdentifier());

        $books = $this->entityManager->getRepository(Book::class)->findBy(['owner' => $currentUser->getId()]);

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
    public function removeBook(Book $book, EntityManagerInterface $entityManager): JsonResponse
    {
        $entityManager->remove($book);
        $entityManager->flush();

        return new JsonResponse(['message' => 'Book removed successfully.'], Response::HTTP_OK);
    }

    #[Route('/api/books/{id}/', name: 'book_update')]
    public function updateBook(Request $request, Book $book, EntityManagerInterface $entityManager): JsonResponse
    {
        // Todo: check if owner by usersession
        $data = json_decode($request->getContent(), true);
        $book->setTitle($data['title'] ?? $book->getTitle());
        $book->setAuthor($data['author'] ?? $book->getAuthor());
        $book->setThumbnail($data['thumbnail'] ?? $book->getThumbnail());
        $book->setDescription($data['description'] ?? $book->getDescription());
        $book->setAvailable($data['status'] ?? $book->isAvailable());

        $entityManager->flush();

        return new JsonResponse(['message' => 'Book updated successfully.'], Response::HTTP_OK);
    }

    #[Route('/books/available_isbn/{isbn}', name: 'book_list_lend')]
    public function getAvailableBooks(EntityManagerInterface $entityManager, string $isbn): JsonResponse
    {
        $currentUser = $this->getSessionUser($this->getUser()->getUserIdentifier());

        if($isbn == 'noIsbn')
            $isbn = null;

        $books = $entityManager->getRepository(Book::class)->getAvailableNotOwner($currentUser->getId(), $isbn);

        $data = [];


        foreach ($books as $book) {

            $reviews = $entityManager->getRepository(BookReview::class)->findBy(['book' => $book->getId()]);

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
    public function getMainAvailableBooks(EntityManagerInterface $entityManager): JsonResponse
    {
        $mainBooks = $entityManager->getRepository(MainBookImage::class)->findAll();

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
    public function requestBook(Request $request, Book $book, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $bookRequest = new BookRequest();
        $currentUser = $this->getSessionUser($this->getUser()->getUserIdentifier());
        $performedBy = $entityManager->getRepository(User::class)->find($currentUser->getId());

        $bookRequest->setBook($book);
        $bookRequest->setRequestDate(date("Y-m-d h:i:sa"));
        $bookRequest->setReturnDate($data['date']);
        $bookRequest->setIsActive(1);
        $bookRequest->setIsLent(0);
        $bookRequest->setIsReturn(0);
        $bookRequest->setRequestedBy($performedBy);

        $bookHistory = new BookHistory();

        $bookHistory->setBook($book);
        $bookHistory->setDateCreated(date("Y-m-d h:i:sa"));
        // Todo: Book requested
        $bookHistory->setAction(2);
        $bookHistory->setIsRequest(false);
        $bookHistory->setPerformedBy($performedBy);

        $entityManager->persist($bookHistory);
        $entityManager->persist($bookRequest);
        $entityManager->flush();

        return new JsonResponse(['message' => 'Book updated successfully.'], Response::HTTP_OK);
    }

    #[Route('/books/requests/', name: 'book_request_list')]
    public function getBookRequests(EntityManagerInterface $entityManager): JsonResponse
    {
        $data = [];

        $currentUser = $this->getSessionUser($this->getUser()->getUserIdentifier());
        $bookRequests = $entityManager->getRepository(BookRequest::class)->getBookRequests($currentUser->getId());

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
    public function getExpiredRequests(EntityManagerInterface $entityManager): JsonResponse
    {
        $currentUser = $this->getSessionUser($this->getUser()->getUserIdentifier());
        $requests = $entityManager->getRepository(BookRequest::class)->findBy(['requestedBy' => $currentUser->getId(), 'isLent'=> 1]);

//        foreach ($bookRequests as $bookRequest) {
//            $book = $bookRequest->getBook();
//            $data[] = [
//                'id' => $bookRequest->getId(),
//                'book' => [
//                    'id' => $book->getId(),
//                    'title' => $book->getTitle(),
//                    'author' => $book->getAuthor(),
//                    'thumbnail' => $book->getThumbnail(),
//                    'description' => $book->getDescription(),
//                    'status' => $book->isAvailable(),
//                ],
//                'requestDate' => $bookRequest->getRequestDate(),
//                'returnDate' => $bookRequest->getReturnDate(),
//                'requestedBy' => [
//                    'id' => $bookRequest->getRequestedBy()->getId(),
//                    'username' => $bookRequest->getRequestedBy()->getUsername(),
//                    'rating' => $bookRequest->getRequestedBy()->getRating(),
//                    'phone' => $bookRequest->getRequestedBy()->getNumber()
//                ]
//            ];
//        }

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
    public function acceptRequest(BookRequest $bookRequest, EntityManagerInterface $entityManager): JsonResponse
    {
        $currentUser = $this->getSessionUser($this->getUser()->getUserIdentifier());

        $bookHistory = new BookHistory();
        $book = $bookRequest->getBook();

        $bookHistory->setBook($bookRequest->getBook());
        $bookHistory->setDateCreated(date("Y-m-d h:i:sa"));
        // Todo: book request accepted
        $bookHistory->setAction(3);
        $bookHistory->setIsRequest(false);
        $bookHistory->setPerformedBy($currentUser);

        $bookRequest->setIsActive(0);
        $bookRequest->setIsLent(1);
        $book->setAvailable(0);
        $book->setLendedTo($currentUser);

        $entityManager->persist($book);
        $entityManager->persist($bookHistory);
        $entityManager->persist($bookRequest);
        $entityManager->flush();

        return new JsonResponse(['message' => 'Book request accepted.'], Response::HTTP_OK);
    }

    #[Route('/books/requests/{id}/decline', name: 'book_lend_request_decline')]
    public function declineRequest(BookRequest $bookRequest, EntityManagerInterface $entityManager): JsonResponse
    {
        // Todo: performed by UserSession
        $currentUser = $this->getSessionUser($this->getUser()->getUserIdentifier());
        $performedBy = $entityManager->getRepository(User::class)->find($currentUser->getId());
        $bookHistory = new BookHistory();

        $bookHistory->setBook($bookRequest->getBook());
        $bookHistory->setDateCreated(date("Y-m-d h:i:sa"));
        // Todo: book request declined
        $bookHistory->setAction(4);
        $bookHistory->setIsRequest(false);
        $bookHistory->setPerformedBy($performedBy);

        $bookRequest->setIsActive(0);

        $entityManager->persist($bookHistory);
        $entityManager->persist($bookRequest);
        $entityManager->flush();

        return new JsonResponse(['message' => 'Book request accepted.'], Response::HTTP_OK);
    }

    #[Route('/books/lent', name: 'book_lent')]
    public function getLentBooks(EntityManagerInterface $entityManager): JsonResponse
    {
        $currentUser = $this->getSessionUser($this->getUser()->getUserIdentifier());
        $requests = $entityManager->getRepository(BookRequest::class)->findBy(['requestedBy' => $currentUser->getId(), 'isReturn' => 0,]);

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
    public function returnBook(Request $request, EntityManagerInterface $entityManager, BookRequest $bookRequest): JsonResponse
    {
        $rating = 0;
        $bookRating = 0;

        $currentUser = $this->getSessionUser($this->getUser()->getUserIdentifier());
        $data = json_decode($request->getContent(), true);
        $bookRequest->getBook()->setLendedTo(null);
        $bookRequest->setIsLent(0);

        $reviews = $entityManager->getRepository(UserReview::class)->findBy(['user' => $bookRequest->getBook()->getOwner()->getId()]);

        if(count($reviews) != 0)
        {
            foreach ($reviews as $review)
                $rating = $rating + $review->getRating();

            $rating = ($rating + (int)$data['lenderRating']) / (count($reviews) + 1);
        }
        else
            $rating = $rating + (int)$data['lenderRating'];

        $bookRequest->getBook()->getOwner()->setRating($rating);

        $bookReviews = $entityManager->getRepository(BookReview::class)->findBy(['book' => $bookRequest->getBook()]);

        if(count($bookReviews) != 0)
        {
            foreach ($bookReviews as $review)
                $bookRating = $bookRating + $review->getRating();

            $bookRating = ($bookRating + (int)$data['bookRating']) / (count($bookReviews) + 1);
        }
        else
            $bookRating = $bookRating + (int)$data['bookRating'];

        $bookRequest->getBook()->setRating($bookRating);

        $userReview = new UserReview();

        $userReview->setUser($bookRequest->getBook()->getOwner());
        $userReview->setReview($data['lenderReview']);
        $userReview->setRating((int)$data['lenderRating']);

        $bookReview = new BookReview();

        $bookReview->setbook($bookRequest->getBook());
        $bookReview->setReview($data['bookReview']);
        $bookReview->setRating((int)$data['bookRating']);

        $bookRequest->setIsReturn(1);

        $bookHistory = new BookHistory();

        $bookHistory->setBook($bookRequest->getBook());
        $bookHistory->setDateCreated(date("Y-m-d h:i:sa"));
        // Todo: Return Requested
        $bookHistory->setAction(5);
        $bookHistory->setIsRequest(false);
        $bookHistory->setPerformedBy($entityManager->getRepository(User::class)->find($currentUser->getId()));

        $entities = [
          $bookHistory,
          $bookReview,
          $userReview
        ];

        foreach ($entities as $entity)
        {
            $entityManager->persist($entity);
        }

        $entityManager->flush();

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

        return new JsonResponse(['message' => 'Book request accepted.'], Response::HTTP_OK);;
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
        $rating = 0;

        $currentUser = $this->getSessionUser($this->getUser()->getUserIdentifier());
        $data = json_decode($request->getContent(), true);
        $reviews = $entityManager->getRepository(UserReview::class)->findBy(['user' => $user->getId()]);

        if(count($reviews) != 0)
        {
            foreach ($reviews as $review)
                $rating = $rating + $review->getRating();

            $rating = ($rating + (int)$data['lenderRating']) / (count($reviews) + 1);
        }
        else
            $rating = $rating + (int)$data['lenderRating'];

        $user->setRating($rating);

        $userReview = new UserReview();

        $userReview->setUser($user);
        $userReview->setReview($data['lenderReview']);
        $userReview->setRating((int)$data['lenderRating']);

        $book->setAvailable(1);
        $bookRequest->setIsReturn(0);

        $bookHistory = new BookHistory();

        $bookHistory->setBook($book);
        $bookHistory->setDateCreated(date("Y-m-d h:i:sa"));
        // Todo: Return Accepted
        $bookHistory->setAction(6);
        $bookHistory->setIsRequest(false);
        // Todo: user session
        $bookHistory->setPerformedBy($currentUser);

        $entities = [
            $bookHistory,
            $userReview
        ];

        foreach ($entities as $entity)
        {
            $entityManager->persist($entity);
        }

        $entityManager->flush();

        return new JsonResponse(['message' => 'Book request accepted.'], Response::HTTP_OK);
    }
}
