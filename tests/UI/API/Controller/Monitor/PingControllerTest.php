<?php

namespace Tests\Leos\UI\API\Controller\Monitor;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class PingControllerTest extends WebTestCase
{

    public function testPingEndpoint()
    {
        $client = self::createClient();

        $client->request('GET', '/api/ping');

        $response = $client->getResponse();

        self::assertEquals(200, $response->getStatusCode());
        self::assertEquals('application/json', $response->headers->get('content-type'));
        self::assertContains('pong', $response->getContent());
    }
}
