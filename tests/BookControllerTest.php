<?php

namespace App\Tests;


use App\Controller\BookController;
use App\Entity\Book;
use App\Entity\BookRequest;
use App\Entity\User;
use App\Factory\BookFactory;
use App\Factory\BookHistoryFactory;
use App\Factory\ReviewFactory;
use App\Model\ReviewModel;
use App\Repository\BookRepository;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class BookControllerTest extends TestCase
{
    private $entityManager;
    private $reviewFactory;
    private $reviewModel;
    private $bookHistoryFactory;
    private $bookFactory;

    protected function setUp(): void
    {
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->reviewFactory = $this->createMock(ReviewFactory::class);
        $this->reviewModel = $this->createMock(ReviewModel::class);
        $this->bookHistoryFactory = $this->createMock(BookHistoryFactory::class);
        $this->bookFactory = $this->createMock(BookFactory::class);
    }

    public function testAddBook(): void
    {
        $controller = $this->createController();

        $request = Request::create('/books/add_book', 'POST');
        $response = $controller->addBook($request);

        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('application/json', $response->headers->get('Content-Type'));

        $responseData = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('message', $responseData);
        $this->assertEquals('User created successfully', $responseData['message']);
    }

    public function testAddBookManual(): void
    {
        $controller = $this->createController();

        $request = Request::create('/books/add_book_manual', 'POST');
        $response = $controller->addBookManual($request);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());

        $responseData = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('message', $responseData);
        $this->assertEquals('Book added successfully!', $responseData['message']);
    }

    public function testGetBookImage(): void
    {
        $controller = $this->createController();
        $book = new Book();

        $response = $controller->getBookImage($book);

        $this->assertInstanceOf(BinaryFileResponse::class, $response);
        $this->assertEquals('image/png', $response->headers->get('Content-Type'));
    }

    private function createController(): BookController
    {
        return new BookController(
            $this->bookHistoryFactory,
            $this->reviewModel,
            $this->reviewFactory,
            $this->entityManager,
            $this->bookFactory
        );
    }

    public function testGetBooks(): void
    {
        $controller = $this->createController();

        $request = Request::create('/books/', 'GET');
        $response = $controller->getBooks($request);

        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('application/json', $response->headers->get('Content-Type'));

        $responseData = json_decode($response->getContent(), true);
    }

    public function testGetBookHistory(): void
    {
        $controller = $this->createController();

        $book = new Book();

        $request = Request::create('/api/books/' . $book->getId() . '/history', 'GET');
        $response = $controller->getBookHistory($book);

        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('application/json', $response->headers->get('Content-Type'));

        $responseData = json_decode($response->getContent(), true);
    }

    public function testRemoveBook(): void
    {
        $controller = $this->createController();
        $book = new Book();

        $response = $controller->removeBook($book);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());

        $responseData = json_decode($response->getContent(), true);
    }

    public function testUpdateBook(): void
    {
        $controller = $this->createController();
        $book = new Book();

        $request = Request::create('/api/books/' . $book->getId() . '/', 'PUT', [], [], [], [], json_encode([]));
        $response = $controller->updateBook($request, $book);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());

        $responseData = json_decode($response->getContent(), true);
    }

    public function testGetAvailableBooks(): void
    {
        $controller = $this->createController();

        $request = Request::create('/books/available_isbn/{isbn}', 'GET');
        $request->attributes->set('isbn', '1234567890');

        $response = $controller->getAvailableBooks('1234567890');

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());

        $responseData = json_decode($response->getContent(), true);
    }

    public function testGetMainAvailableBooks(): void
    {
        $controller = $this->createController();

        $request = Request::create('/books/available', 'GET');
        $response = $controller->getMainAvailableBooks($request);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());

        $responseData = json_decode($response->getContent(), true);
    }

    public function testGetLentBooks(): void
    {
        $controller = $this->createController();

        $request = Request::create('/books/lent', 'GET');
        $response = $controller->getLentBooks($request);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());

        $responseData = json_decode($response->getContent(), true);
    }

    public function testReturnBook(): void
    {
        $controller = $this->createController();
        $bookRequest = new BookRequest();

        $request = Request::create('/books/lent/{id}/return', 'POST', [], [], [], [], json_encode([]));
        $response = $controller->returnBook($request, $bookRequest);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());

        $responseData = json_decode($response->getContent(), true);
    }

    public function testGetReturnedBooks(): void
    {
        $controller = $this->createController();

        $request = Request::create('/books/returned', 'GET');
        $response = $controller->getReturnedBooks($request);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());

        $responseData = json_decode($response->getContent(), true);
    }

    public function testGetMyLentBooks(): void
    {
        $controller = $this->createController();

        $request = Request::create('/books/my_lent', 'GET');
        $response = $controller->getMyLentBooks($request);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());

        $responseData = json_decode($response->getContent(), true);
    }

    public function testGetMyRequestsBooks(): void
    {
        $controller = $this->createController();

        $request = Request::create('/requests/pending', 'GET');
        $response = $controller->getMyRequestsBooks($request);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());

        $responseData = json_decode($response->getContent(), true);
    }

    public function testAcceptReturnedBook(): void
    {
        $controller = $this->createController();
        $book = new Book();
        $user = new User();
        $bookRequest = new BookRequest();

        $request = Request::create('/books/lent/{id_book}/{id_user}/{id_request}/accept', 'POST', [], [], [], [], json_encode([]));
        $response = $controller->acceptReturnedBook($request, $book, $user, $bookRequest);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());

        $responseData = json_decode($response->getContent(), true);
    }
}

