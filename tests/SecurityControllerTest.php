<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityControllerTest extends WebTestCase
{
    public function testLogin(): void
    {
        $client = static::createClient();
        $client->request(
            Request::METHOD_POST,
            '/login',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            '{"username":"testuser","password":"testpassword"}'
        );

        $response = $client->getResponse();

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertJson($response->getContent());

        $responseData = json_decode($response->getContent(), true);

        $this->assertArrayHasKey('token', $responseData);
    }

    public function testRegister(): void
    {
        $client = static::createClient();
        $client->request(
            Request::METHOD_POST,
            '/register',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            '{"username":"testuser","password":"testpassword","email":"test@example.com","region":"Region","phone_number":"123456789","city":"City","other_contacts":"Contacts"}'
        );

        $response = $client->getResponse();

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertJson($response->getContent());

        $responseData = json_decode($response->getContent(), true);

        $this->assertArrayHasKey('message', $responseData);
    }

    public function testLogout(): void
    {
        $client = static::createClient();
        $client->request(Request::METHOD_GET, '/logout');

        $response = $client->getResponse();

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
    }
}
