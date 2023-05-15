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
    private ReviewFactory $reviewFactory;
    private ReviewModel $reviewModel;
    private BookHistoryFactory $bookHistoryFactory;
    private BookFactory $bookFactory;


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

        if ($searchQuery) {
            $books = $this->entityManager->getRepository(Book::class)
                ->searchByTitleAndAuthor($searchQuery, $currentUser->getId());
        }
        else {
            $books = $this->entityManager->getRepository(Book::class)->findBy(['owner'=>$currentUser->getId()]);
        }


//        $books = $this->entityManager->getRepository(Book::class)
//            ->searchByTitleAndAuthor($searchQuery, $currentUser->getId());

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

    #[Route('/books/lent', name: 'book_lent')]
    public function getLentBooks(): JsonResponse
    {
        $currentUser = $this->getSessionUser($this->getUser()->getUserIdentifier());
        $requests = $this->entityManager->getRepository(BookRequest::class)->findBy(['requestedBy' => $currentUser->getId(), 'isReturn' => 0, 'isLent' => 1]);

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
    public function getReturnedBooks(): JsonResponse
    {
        $currentUser = $this->getSessionUser($this->getUser()->getUserIdentifier());

        $requests = $this->entityManager->getRepository(BookRequest::class)->findBy(['isReturn' => 1]);

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
    public function getMyLentBooks(): JsonResponse
    {
        $currentUser = $this->getSessionUser($this->getUser()->getUserIdentifier());

        $requests = $this->entityManager->getRepository(BookRequest::class)->getMyLentBooks($currentUser->getId());

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
    public function getMyRequestsBooks(): JsonResponse
    {
        $currentUser = $this->getSessionUser($this->getUser()->getUserIdentifier());

        $requests = $this->entityManager->getRepository(BookRequest::class)->findBy(['isActive' => 1, 'requestedBy' => $currentUser->getId()]);

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

    /**
     * @Route("/books/lent/{id_book}/{id_user}/{id_request}/accept")
     *
     * @ParamConverter("book", options={"mapping": {"id_book" : "id"}})
     * @ParamConverter("user", options={"mapping": {"id_user" : "id"}})
     * @ParamConverter("bookRequest", options={"mapping": {"id_request" : "id"}})
     */
    public function acceptReturnedBook(Request $request, Book $book, User $user, BookRequest $bookRequest): JsonResponse
    {
        $currentUser = $this->getSessionUser($this->getUser()->getUserIdentifier());
        $data = json_decode($request->getContent(), true);
        $reviews = $this->entityManager->getRepository(UserReview::class)->findBy(['user' => $user->getId()]);

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
