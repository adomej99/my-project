<?php

namespace App\Tests;

use App\Controller\RequestsController;
use App\Entity\Book;
use App\Entity\BookRequest;
use App\Entity\User;
use App\Factory\BookHistoryFactory;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class RequestsControllerUnitTest extends TestCase
{
    public function testRequestBookSetsReturnDate(): void
    {
        // Create a mock of the Book entity
        $book = $this->createMock(Book::class);

        // Create a mock of the Request object with the necessary content
        $request = $this->createMock(Request::class);
        $requestContent = json_encode(['date' => '2023-05-15']);
        $request->expects($this->once())
            ->method('getContent')
            ->willReturn($requestContent);

        // Create a mock of the User entity
        $user = $this->createMock(User::class);

        // Create a mock of the EntityManagerInterface
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $entityManager->expects($this->once())
            ->method('getRepository')
            ->willReturn($user);
        $entityManager->expects($this->once())
            ->method('persist');
        $entityManager->expects($this->once())
            ->method('flush');

        // Create a mock of the BookHistoryFactory
        $bookHistoryFactory = $this->createMock(BookHistoryFactory::class);
        $bookHistoryFactory->expects($this->once())
            ->method('createHistory');

        // Create an instance of the RequestsController class and pass the mock dependencies
        $controller = new RequestsController($bookHistoryFactory, $entityManager);

        // Call the requestBook method with the necessary parameters
        $controller->requestBook($request, $book);

        // Assert that the return date of the book request is set correctly
        $this->assertEquals('2023-05-15', $book->getRequestDate());
    }

    public function testRequestBookReturnsJsonResponse(): void
    {
        $book = $this->createMock(Book::class);

        $request = $this->createMock(Request::class);
        $requestContent = json_encode(['date' => '2023-05-15']);
        $request->expects($this->once())
            ->method('getContent')
            ->willReturn($requestContent);

        $user = $this->createMock(User::class);

        $entityManager = $this->createMock(EntityManagerInterface::class);
        $entityManager->expects($this->once())
            ->method('getRepository')
            ->willReturn($user);
        $entityManager->expects($this->once())
            ->method('persist');
        $entityManager->expects($this->once())
            ->method('flush');

        $bookHistoryFactory = $this->createMock(BookHistoryFactory::class);
        $bookHistoryFactory->expects($this->once())
            ->method('createHistory');

        $controller = new RequestsController($bookHistoryFactory, $entityManager);

        $response = $controller->requestBook($request, $book);

        $this->assertInstanceOf(JsonResponse::class, $response);
    }

    public function testRequestBookCreatesHistory(): void
    {
        $book = $this->createMock(Book::class);

        $request = $this->createMock(Request::class);
        $requestContent = json_encode(['date' => '2023-05-15']);
        $request->expects($this->once())
            ->method('getContent')
            ->willReturn($requestContent);

        $user = $this->createMock(User::class);

        $entityManager = $this->createMock(EntityManagerInterface::class);
        $entityManager->expects($this->once())
            ->method('getRepository')
            ->willReturn($user);
        $entityManager->expects($this->once())
            ->method('persist');
        $entityManager->expects($this->once())
            ->method('flush');

        $bookHistoryFactory = $this->createMock(BookHistoryFactory::class);
        $bookHistoryFactory->expects($this->once())
            ->method('createHistory')
            ->with($user, $book, 2, false);

        $controller = new RequestsController($bookHistoryFactory, $entityManager);

        $controller->requestBook($request, $book);
    }

    public function testRequestBookReturnsSuccessfulResponse(): void
    {
        $book = $this->createMock(Book::class);

        $request = $this->createMock(Request::class);
        $requestContent = json_encode(['date' => '2023-05-15']);
        $request->expects($this->once())
            ->method('getContent')
            ->willReturn($requestContent);

        $user = $this->createMock(User::class);

        $entityManager = $this->createMock(EntityManagerInterface::class);
        $entityManager->expects($this->once())
            ->method('getRepository')
            ->willReturn($user);
        $entityManager->expects($this->once())
            ->method('persist');
        $entityManager->expects($this->once())
            ->method('flush');

        $bookHistoryFactory = $this->createMock(BookHistoryFactory::class);
        $bookHistoryFactory->expects($this->once())
            ->method('createHistory');

        $controller = new RequestsController($bookHistoryFactory, $entityManager);

        $response = $controller->requestBook($request, $book);

        $this->assertInstanceOf(JsonResponse::class, $response);

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());

        $responseData = json_decode($response->getContent(), true);
        $this->assertEquals(['message' => 'Book updated successfully.'], $responseData);
    }

    public function testRequestBookWithInvalidDate(): void
    {
        $book = $this->createMock(Book::class);

        $request = $this->createMock(Request::class);
        $requestContent = json_encode(['date' => '2023-05-']);
        $request->expects($this->once())
            ->method('getContent')
            ->willReturn($requestContent);

        $entityManager = $this->createMock(EntityManagerInterface::class);

        $bookHistoryFactory = $this->createMock(BookHistoryFactory::class);

        $controller = new RequestsController($bookHistoryFactory, $entityManager);

        $response = $controller->requestBook($request, $book);

        $this->assertInstanceOf(JsonResponse::class, $response);

        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());

        $responseData = json_decode($response->getContent(), true);
        $this->assertEquals(['message' => 'Invalid request data.'], $responseData);
    }

    public function testRequestBookPersistsBookRequestWithCorrectValues(): void
    {
        $book = $this->createMock(Book::class);

        $request = $this->createMock(Request::class);
        $requestContent = json_encode(['date' => '2023-05-15']);
        $request->expects($this->once())
            ->method('getContent')
            ->willReturn($requestContent);

        $user = $this->createMock(User::class);

        $entityManager = $this->createMock(EntityManagerInterface::class);
        $entityManager->expects($this->once())
            ->method('getRepository')
            ->willReturn($user);
        $entityManager->expects($this->once())
            ->method('persist')
            ->willReturnCallback(function (BookRequest $bookRequest) use ($book, $user) {
                $this->assertSame($book, $bookRequest->getBook());
                $this->assertSame($user, $bookRequest->getRequestedBy());
                $this->assertEquals('2023-05-15', $bookRequest->getReturnDate());
                $this->assertEquals(1, $bookRequest->getIsActive());
                $this->assertEquals(0, $bookRequest->getIsLent());
                $this->assertEquals(0, $bookRequest->getIsReturn());
            });
        $entityManager->expects($this->once())
            ->method('flush');

        $bookHistoryFactory = $this->createMock(BookHistoryFactory::class);
        $bookHistoryFactory->expects($this->once())
            ->method('createHistory');

        $controller = new RequestsController($bookHistoryFactory, $entityManager);

        $controller->requestBook($request, $book);
    }

    public function testRequestBookCreatesHistoryWithCorrectValues(): void
    {
        $book = $this->createMock(Book::class);

        $request = $this->createMock(Request::class);
        $requestContent = json_encode(['date' => '2023-05-15']);
        $request->expects($this->once())
            ->method('getContent')
            ->willReturn($requestContent);

        $user = $this->createMock(User::class);

        $entityManager = $this->createMock(EntityManagerInterface::class);
        $entityManager->expects($this->once())
            ->method('getRepository')
            ->willReturn($user);
        $entityManager->expects($this->once())
            ->method('persist');
        $entityManager->expects($this->once())
            ->method('flush');

        $bookHistoryFactory = $this->createMock(BookHistoryFactory::class);
        $bookHistoryFactory->expects($this->once())
            ->method('createHistory')
            ->willReturnCallback(function (User $currentUser, Book $requestedBook, int $action, bool $isRequest) use ($user, $book) {
                $this->assertSame($user, $currentUser);
                $this->assertSame($book, $requestedBook);
                $this->assertEquals(2, $action);
                $this->assertFalse($isRequest);
            });

        $controller = new RequestsController($bookHistoryFactory, $entityManager);

        $controller->requestBook($request, $book);
    }

    public function testRequestBook_BookNotFound()
    {
        $bookId = 123; // Invalid book ID

        $requestPayload = [
            'date' => '2023-05-30',
        ];

        $response = $this->client->request('POST', '/books/available/' . $bookId, ['json' => $requestPayload]);

        // Assert the HTTP status code of the response
        $this->assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());

        // Assert the content of the response
        $responseData = json_decode($response->getContent(), true);
        $this->assertEquals(['message' => 'Book not found.'], $responseData);
    }

    public function testRequestBook_InvalidDate()
    {
        $bookId = 1; // Valid book ID

        $requestPayload = [
            'date' => '2023-13-45', // Invalid date format
        ];

        $response = $this->client->request('POST', '/books/available/' . $bookId, ['json' => $requestPayload]);

        // Assert the HTTP status code of the response
        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());

        // Assert the content of the response
        $responseData = json_decode($response->getContent(), true);
        $this->assertEquals(['message' => 'Invalid date format.'], $responseData);
    }

    public function testRequestBook_ValidRequest()
    {
        $bookId = 1;

        $requestPayload = [
            'date' => '2023-05-30',
        ];

        $response = $this->client->request('POST', '/books/available/' . $bookId, ['json' => $requestPayload]);

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());

        $responseData = json_decode($response->getContent(), true);
        $this->assertEquals(['message' => 'Book updated successfully.'], $responseData);
    }

    public function testRequestBook_MissingDate()
    {
        $bookId = 1; // Valid book ID

        $requestPayload = [
            // 'date' => '2023-05-30' (Missing date field)
        ];

        $response = $this->client->request('POST', '/books/available/' . $bookId, ['json' => $requestPayload]);

        // Assert the HTTP status code of the response
        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());

        // Assert the content of the response
        $responseData = json_decode($response->getContent(), true);
        $this->assertEquals(['message' => 'Missing required field: date.'], $responseData);
    }

    public function testGetBookRequests_NoBookRequests()
    {
        $this->simulateUserSession();

        $response = $this->client->request('GET', '/books/requests');

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());

        $responseData = json_decode($response->getContent(), true);
        $this->assertEquals([], $responseData);
    }

    public function testGetBookRequests_WithBookRequests()
    {
        $this->simulateUserSession();

        $response = $this->client->request('GET', '/books/requests');

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());

        $responseData = json_decode($response->getContent(), true);

        $this->assertArrayHasKey('id', $responseData[0]);
        $this->assertArrayHasKey('book', $responseData[0]);
        $this->assertArrayHasKey('requestDate', $responseData[0]);
        $this->assertArrayHasKey('returnDate', $responseData[0]);
        $this->assertArrayHasKey('requestedBy', $responseData[0]);
    }

    public function testAcceptRequest_ValidRequest()
    {
        $this->simulateUserSession();

        $response = $this->client->request('POST', '/books/requests/{id}/accept');

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());

        $responseData = json_decode($response->getContent(), true);
        $this->assertEquals(['message' => 'Book request accepted.'], $responseData);
    }

    public function testAcceptRequest_InvalidRequest()
    {
        $this->simulateUserSession();

        $response = $this->client->request('POST', '/books/requests/{id}/accept');

        $this->assertEquals(Response::HTTP_FORBIDDEN, $response->getStatusCode());

        $responseData = json_decode($response->getContent(), true);
        $this->assertEquals(['message' => 'You are not allowed to accept this book request.'], $responseData);
    }

    public function testAcceptRequest_BookAlreadyLent()
    {
        $this->simulateUserSession();

        $response = $this->client->request('POST', '/books/requests/{id}/accept');

        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());

        $responseData = json_decode($response->getContent(), true);
        $this->assertEquals(['message' => 'The book is already lent to another user.'], $responseData);
    }

    public function testAcceptRequest_BookNotFound()
    {
        $this->simulateUserSession();

        $response = $this->client->request('POST', '/books/requests/{id}/accept');

        $this->assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());

        $responseData = json_decode($response->getContent(), true);
        $this->assertEquals(['message' => 'The book could not be found.'], $responseData);
    }

    public function testAcceptRequest_Success()
    {
        $this->simulateUserSession();

        $response = $this->client->request('POST', '/books/requests/{id}/accept');

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());

        $responseData = json_decode($response->getContent(), true);
        $this->assertEquals(['message' => 'Book request accepted.'], $responseData);
    }

    public function testAcceptRequest_UnauthorizedUser()
    {
        $response = $this->client->request('POST', '/books/requests/{id}/accept');

        $this->assertEquals(Response::HTTP_FORBIDDEN, $response->getStatusCode());

        $responseData = json_decode($response->getContent(), true);
        $this->assertEquals(['message' => 'You are not authorized to accept this book request.'], $responseData);
    }
}
