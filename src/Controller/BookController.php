<?php

namespace App\Controller;

use App\Entity\Book;
use App\Entity\BookHistory;
use App\Entity\BookRequest;
use App\Entity\BookReview;
use App\Entity\User;
use App\Entity\UserReview;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BookController extends AbstractController
{
    #[Route('/books/add_book', name: 'add_book')]
    public function addBook(Request $request, EntityManagerInterface $entityManager) : Response
    {
        $book = new Book();
        $content = json_decode($request->getContent(), true);
        // Todo: get owner from usersession
        $owner = $entityManager->getRepository(User::class)->find($content['userId']);

        $book->setTitle($content['title']);
        $book->setAuthor($content['author']);
        $book->setThumbnail($content['thumbnail']);
        $book->setDescription(substr($content['description'], 0, 250));
        $book->setOwner($owner);

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
        // Get the data sent from the front-end
        $data = $request->request->all();

//        return new JsonResponse(['message' => $data]);

        // Create a new Book entity and set its properties
        $book = new Book();
        $book->setTitle($data['title']);
        $book->setAuthor($data['author']);
        $book->setDescription($data['description']);

        // Upload the image and save its path to the database
//        $file = $request->files->get('image');
//        if ($file) {
//            $filename = uniqid() . '.' . $file->guessExtension();
//            $file->move($this->getParameter('book_images_directory'), $filename);
//            $book->setThumbnail($filename);
//        }

        $decodedImageData = base64_decode($data['thumbnail']);

        $filename = uniqid() . '.jpg';

        $file = fopen($this->getParameter('book_images_directory') . $filename, 'w');

        fwrite($file, $decodedImageData);

        fclose($file);

        $book->setThumbnail($filename);

        // Set the user who added the book
//        $user = $this->getUser();
        $book->setOwner($entityManager->getRepository(User::class)->find(10));

        // Save the book to the database
        $entityManager->persist($book);
        $entityManager->flush();

        // Return a response to the front-end
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

    #[Route('/books', name: 'get_books')]
    public function getBooks(EntityManagerInterface $entityManager) : Response
    {
        // Todo: get book by owner from usersession
        $books = $entityManager->getRepository(Book::class)->findBy(['owner' => 10]);

        $this->getUser();

        $data = [];

        foreach ($books as $book) {
            $thumbnail = $book->getThumbnail();

            if(!str_contains($thumbnail, 'http'))
            {
                //            $path = $this->getParameter('book_images_directory').$thumbnail;
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

    #[Route('/books/available/', name: 'book_list_lend')]
    public function getAvailableBooks(EntityManagerInterface $entityManager): JsonResponse
    {
        $books = $entityManager->getRepository(Book::class)->findBy(['available' => 1]);

        $data = [];

        foreach ($books as $book) {
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
            ];
        }

        return new JsonResponse($data);
    }

    #[Route('/books/available/{id}', name: 'book_lend_request')]
    public function requestBook(Request $request, Book $book, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $bookRequest = new BookRequest();
        // Todo: performed by UserSession
        $performedBy = $entityManager->getRepository(User::class)->find(10);

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

        // Todo: get requests by user session if owner
        $bookRequests = $entityManager->getRepository(BookRequest::class)->findBy(['isActive' => 1, 'isLent' => 0]);

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
                    'rating' => $bookRequest->getRequestedBy()->getRating()
                ]
            ];
        }

        return new JsonResponse($data);
    }

    #[Route('/books/requests/{id}/accept', name: 'book_lend_request_accept')]
    public function acceptRequest(BookRequest $bookRequest, EntityManagerInterface $entityManager): JsonResponse
    {
        // Todo: performed by UserSession
        $performedBy = $entityManager->getRepository(User::class)->find(10);
        $bookHistory = new BookHistory();
        $book = $bookRequest->getBook();

        $bookHistory->setBook($bookRequest->getBook());
        $bookHistory->setDateCreated(date("Y-m-d h:i:sa"));
        // Todo: book request accepted
        $bookHistory->setAction(3);
        $bookHistory->setIsRequest(false);
        $bookHistory->setPerformedBy($performedBy);

        $bookRequest->setIsActive(0);
        $bookRequest->setIsLent(1);
        $book->setAvailable(0);

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
        $performedBy = $entityManager->getRepository(User::class)->find(10);
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
        $listOwner = $entityManager->getRepository(User::class)->find(10);

        // Todo: find by UserSession
        $requests = $entityManager->getRepository(BookRequest::class)->findBy(['requestedBy' => 10, 'isReturn' => 0,]);

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
        $data = json_decode($request->getContent(), true);
        $rating = 0;
        $bookRating = 0;

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
        $bookHistory->setPerformedBy($entityManager->getRepository(User::class)->find(10));

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
        $listOwner = $entityManager->getRepository(User::class)->find(10);

        // Todo: find by user session
        $requests = $entityManager->getRepository(BookRequest::class)->findBy(['isReturn' => 1]);

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
        $bookHistory->setPerformedBy($entityManager->getRepository(User::class)->find(10));

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
