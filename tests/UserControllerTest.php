<?php

namespace App\Tests;

use PHPUnit\Framework\TestCase;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class UserControllerTest extends WebTestCase
{
    public function testGetUserInfo(): void
    {
        $client = static::createClient();
        $client->request(Request::METHOD_GET, '/users/1');

        $response = $client->getResponse();

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertJson($response->getContent());
    }

    public function testGetUserRole(): void
    {
        $client = static::createClient();
        $client->request(Request::METHOD_GET, '/user/role');

        $response = $client->getResponse();

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertJson($response->getContent());
    }

    public function testGetUsers(): void
    {
        $client = static::createClient();
        $client->request(Request::METHOD_GET, '/users');

        $response = $client->getResponse();

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertJson($response->getContent());
    }

    public function testSetUserStatus(): void
    {
        $client = static::createClient();
        $client->request(Request::METHOD_GET, '/users/1/toggle-status');

        $response = $client->getResponse();

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertJson($response->getContent());
    }

    public function testReportPerson(): void
    {
        $client = static::createClient();
        $client->request(
            Request::METHOD_POST,
            '/users/report',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            '{"reportedBy": 1, "reportedPerson": 2, "requestId": 3, "report": "Some report message"}'
        );

        $response = $client->getResponse();

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertJson($response->getContent());
    }

    public function testGetAllReports(): void
    {
        $client = static::createClient();
        $client->request(Request::METHOD_GET, '/reports');

        $response = $client->getResponse();

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertJson($response->getContent());
    }

    public function testGetReports(): void
    {
        $client = static::createClient();
        $client->request(Request::METHOD_GET, '/reports/1');

        $response = $client->getResponse();

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertJson($response->getContent());
    }

    public function testDeclineReport(): void
    {
        $client = static::createClient();
        $client->request(Request::METHOD_GET, '/reports/1/decline');

        $response = $client->getResponse();

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertJson($response->getContent());
    }
}
