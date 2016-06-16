<?php

namespace Tests\BookmarksBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use BookmarksBundle\Entity\Bookmark;
use BookmarksBundle\Entity\Comment;
use Doctrine\ORM\EntityManager;

class RESTControllerTest extends WebTestCase
{
    const EXISTING_BOOKMARK_URL = 'http://google.com';

    const EXISTING_COMMENT_IP = '192.168.0.1';

    /**
     * @var Client
     */
    private $client;

    /**
     * @var EntityManager
     */
    private $entityManager;

    public function setUp()
    {
        $this->client = static::createClient();
        $this->entityManager = $this
            ->client
            ->getContainer()
            ->get('doctrine')
            ->getManager();
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
            '/bookmark/' . $this->getExistingBookmark()->getUid() . '/comment',
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
            '/bookmark/' . $this->getExistingBookmark()->getUid() . '/comment',
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

    public function testUpdateCommentSuccess()
    {
        $comment = $this->getExistingComment()->setCreatedAt(new \DateTime('-10 minutes'));
        $this->entityManager->flush();

        $updateText = 'new comment text';
        $this->client->request(
            Request::METHOD_PUT,
            '/comment/' . $comment->getUid(),
            [],
            [],
            [
                'REMOTE_ADDR' => self::EXISTING_COMMENT_IP,
            ],
            json_encode([
                'text' => $updateText
            ])
        );

        $this->assertEquals(
            Response::HTTP_OK,
            $this->client->getResponse()->getStatusCode()
        );

        $this->assertEquals(
            $updateText,
            $this->getExistingComment()->getText()
        );
    }

    public function testUpdateCommentNotUpdated()
    {
        $comment = $this->getExistingComment()->setCreatedAt(new \DateTime('-2 hours'));
        $this->entityManager->flush();

        $updateText = 'new comment text to update';
        $this->client->request(
            Request::METHOD_PUT,
            '/comment/' . $comment->getUid(),
            [],
            [],
            [
                'REMOTE_ADDR' => self::EXISTING_COMMENT_IP,
            ],
            json_encode([
                'text' => $updateText
            ])
        );

        $this->assertEquals(
            Response::HTTP_OK,
            $this->client->getResponse()->getStatusCode()
        );

        $this->assertNotEquals(
            $updateText,
            $this->getExistingComment()->getText()
        );
    }

    public function testUpdateCommentNotFound()
    {
        $comment = $this->getExistingComment();

        $this->client->request(
            Request::METHOD_PUT,
            '/comment/' . $comment->getUid(),
            [],
            [],
            [],
            json_encode([
                'text' => 'some text'
            ])
        );

        $this->assertEquals(
            Response::HTTP_NOT_FOUND,
            $this->client->getResponse()->getStatusCode()
        );
    }

    /**
     * @dataProvider createUpdateCommentRequestProvider
     *
     * @param $data
     */
    public function testUpdateCommentRequest($data)
    {
        $comment = $this->getExistingComment();

        $this->client->request(
            Request::METHOD_PUT,
            '/comment/' . $comment->getUid(),
            [],
            [],
            [
                'REMOTE_ADDR' => self::EXISTING_COMMENT_IP,
            ],
            json_encode($data)
        );

        $this->assertEquals(
            Response::HTTP_BAD_REQUEST,
            $this->client->getResponse()->getStatusCode()
        );
    }

    public function createUpdateCommentRequestProvider()
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
     * @return Bookmark
     */
    protected function getExistingBookmark()
    {
        return $this
            ->entityManager
            ->getRepository(Bookmark::class)
            ->findOneBy(['url' => self::EXISTING_BOOKMARK_URL]);
    }

    /**
     * @return Comment
     */
    protected function getExistingComment()
    {
        return $this
            ->entityManager
            ->getRepository(Comment::class)
            ->findOneBy(['ip' => self::EXISTING_COMMENT_IP]);
    }
}