<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class RequestControllerTest extends WebTestCase
{
    public function testRequestBook(): void
    {
        $client = static::createClient();
        $bookId = 1;

        $requestData = [
            'date' => '2023-06-01',
        ];

        $client->request(
            Request::METHOD_POST,
            '/books/available/' . $bookId,
            [],
            [],
            [],
            json_encode($requestData)
        );

        $response = $client->getResponse();

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertJson($response->getContent());
    }

    public function testGetBookRequests(): void
    {
        $client = static::createClient();
        $client->request(Request::METHOD_GET, '/books/requests/');

        $response = $client->getResponse();

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertJson($response->getContent());
    }

    public function testGetExpiredRequests(): void
    {
        $client = static::createClient();
        $client->request(Request::METHOD_GET, '/requests/expired');

        $response = $client->getResponse();

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertJson($response->getContent());
    }

    public function testAcceptRequest(): void
    {
        $client = static::createClient();
        $requestId = 1;

        $client->request(Request::METHOD_GET, '/books/requests/' . $requestId . '/accept');

        $response = $client->getResponse();

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertJson($response->getContent());
    }

    public function testDeclineRequest(): void
    {
        $this->assertTrue(true);
        $client = static::createClient();
        $requestId = 1;

        $client->request(Request::METHOD_GET, '/books/requests/' . $requestId . '/decline');

        $response = $client->getResponse();

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertJson($response->getContent());
    }

    public function testCancelPendingRequest(): void
    {
        $client = static::createClient();
        $requestId = 1;

        $client->request(Request::METHOD_GET, '/request/pending/cancel/' . $requestId);

        $response = $client->getResponse();

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertJson($response->getContent());
    }
}
