<?php

namespace Tests\BookmarksBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class RESTControllerTest extends WebTestCase
{
    /**
     * @var Client
     */
    private $client;

    public function setUp()
    {
        $this->client = static::createClient();
    }

    public function testGetLatestBookmarks()
    {
        $this->client->request(Request::METHOD_GET, '/bookmark');

        $this->assertTrue(
            $this->client->getResponse()->isSuccessful()
        );
    }
}