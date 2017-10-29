<?php

namespace Tests\Leos\UI\API\Controller\User;

use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SignUpTest extends WebTestCase
{
    /** @var Client|null */
    private $client;

    public function setUp()
    {
        $this->client = self::createClient();
    }

    public function tearDown()
    {
        $this->client = null;
    }

    public function testSignUpSuccess()
    {
        $this->client->request('POST', '/api/user', [
            'username' => 'jorge',
            'email' => 'j@j.com'
        ]);

        self::assertEquals(201, $this->client->getResponse()->getStatusCode());
        self::assertContains('uuid', $this->client->getResponse()->getContent());
        self::assertContains('username', $this->client->getResponse()->getContent());
        self::assertContains('jorge', $this->client->getResponse()->getContent());
        self::assertContains('email', $this->client->getResponse()->getContent());
        self::assertContains('j@j.com', $this->client->getResponse()->getContent());
    }

    public function testSignUpWithInvalidUsernameMustThrow400()
    {
        $this->client->request('POST', '/api/user', [
            'username' => 'lol',
            'email' => 'j@j.com'
        ]);

        self::assertEquals(400, $this->client->getResponse()->getStatusCode());
        self::assertContains('Username must contain at least 6 characters', $this->client->getResponse()->getContent());
    }
}
