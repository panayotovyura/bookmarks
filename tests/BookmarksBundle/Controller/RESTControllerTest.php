<?php

namespace Tests\BookmarksBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use BookmarksBundle\Entity\Bookmark;

class RESTControllerTest extends WebTestCase
{
    const EXISTING_BOOKMARK_URL = 'http://google.com';
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

        $this->assertEquals(
            10,
            count(
                json_decode($this->client->getResponse()->getContent())
            )
        );
    }

    public function testGetBookmarkByUrlSuccess()
    {
        $this->client->request(Request::METHOD_GET, '/bookmark/' . self::EXISTING_BOOKMARK_URL);

        $this->assertTrue(
            $this->client->getResponse()->isSuccessful()
        );
    }

    public function testGetBookmarkByUrlNotFound()
    {
        $this->client->request(Request::METHOD_GET, '/bookmark/http://not.exist.domain.com');

        $this->assertTrue(
            $this->client->getResponse()->isNotFound()
        );
    }

    public function testCreateBookmarkSuccess()
    {
        $this->client->request(
            Request::METHOD_POST,
            '/bookmark',
            [],
            [],
            [],
            json_encode([
                'url' => 'http://yandex.ru',
            ])
        );

        $this->assertEquals(
            Response::HTTP_CREATED,
            $this->client->getResponse()->getStatusCode()
        );
    }

    public function testCreateBookmarkExist()
    {
        $this->client->request(
            Request::METHOD_POST,
            '/bookmark',
            [],
            [],
            [],
            json_encode([
                'url' => self::EXISTING_BOOKMARK_URL,
            ])
        );

        $this->assertEquals(
            Response::HTTP_OK,
            $this->client->getResponse()->getStatusCode()
        );
    }

    /**
     * @dataProvider createBookmarkInvalidRequestProvider
     *
     * @param $data
     */
    public function testCreateBookmarkInvalidRequest($data)
    {
        $this->client->request(
            Request::METHOD_POST,
            '/bookmark',
            [],
            [],
            [],
            json_encode($data)
        );

        $this->assertEquals(
            Response::HTTP_BAD_REQUEST,
            $this->client->getResponse()->getStatusCode()
        );
    }

    public function createBookmarkInvalidRequestProvider()
    {
        return [
            [
                [],
                [
                    'url' => '',
                ],
                [
                    'url' => 'invalidUrl',
                ],
            ]
        ];
    }

    public function testCreateCommentSuccess()
    {
        $this->client->request(
            Request::METHOD_POST,
            '/bookmark/' . $this->getExistingBookmarkUid() . '/comment',
            [],
            [],
            [],
            json_encode([
                'text' => 'comment text'
            ])
        );

        $this->assertEquals(
            Response::HTTP_CREATED,
            $this->client->getResponse()->getStatusCode()
        );
    }

    /**
     * @dataProvider createCommentInvalidRequestProvider
     *
     * @param $data
     */
    public function testCreateCommentInvalidRequest($data)
    {
        $this->client->request(
            Request::METHOD_POST,
            '/bookmark/' . $this->getExistingBookmarkUid() . '/comment',
            [],
            [],
            [],
            json_encode($data)
        );

        $this->assertEquals(
            Response::HTTP_BAD_REQUEST,
            $this->client->getResponse()->getStatusCode()
        );
    }

    public function createCommentInvalidRequestProvider()
    {
        return [
            [
                [],
                [
                    'text' => '',
                ],
            ]
        ];
    }

    /**
     * @return int
     */
    protected function getExistingBookmarkUid()
    {
        return $this
            ->client
            ->getContainer()
            ->get('doctrine')
            ->getManager()
            ->getRepository(Bookmark::class)
            ->findOneBy(['url' => self::EXISTING_BOOKMARK_URL])
            ->getUid();
    }
}