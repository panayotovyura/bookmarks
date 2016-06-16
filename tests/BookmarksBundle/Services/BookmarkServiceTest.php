<?php

namespace Tests\BookmarksBundle\Services;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use \PHPUnit_Framework_MockObject_MockObject as Mock;
use BookmarksBundle\Entity\Bookmark;
use BookmarksBundle\Services\BookmarkService;

class BookmarkServiceTest extends \PHPUnit_Framework_TestCase
{
    public function testGetLatest()
    {
        $repositoryMock = $this->getMockBuilder(EntityRepository::class)
            ->disableOriginalConstructor()
            ->getMock();

        $repositoryMock->expects($this->once())
            ->method('findBy')
            ->with([], ['createdAt' => 'DESC'], 10)
            ->willReturn([]);

        $bookmarkService = $this->getBookmarkService(null, $repositoryMock);

        $bookmarkService->getLatest();
    }

    public function testGetByUrl()
    {
        $repositoryMock = $this->getMockBuilder(EntityRepository::class)
            ->disableOriginalConstructor()
            ->getMock();

        $url = 'http://some.url';
        $repositoryMock->expects($this->once())
            ->method('findOneBy')
            ->with(['url' => $url])
            ->willReturn(null);

        $bookmarkService = $this->getBookmarkService(null, $repositoryMock);

        $bookmarkService->getByUrl($url);
    }

    public function testSave()
    {
        $entityManagerMock = $this->getMockBuilder(EntityManager::class)
            ->disableOriginalConstructor()
            ->getMock();

        $bookmark = new Bookmark();
        $entityManagerMock->expects($this->once())
            ->method('persist')
            ->with($bookmark)
            ->willReturn(null);

        $entityManagerMock->expects($this->once())
            ->method('flush')
            ->willReturn(null);

        $bookmarkService = $this->getBookmarkService($entityManagerMock);

        $bookmarkService->save($bookmark);
    }

    /**
     * @param Mock|null $entityManagerMock
     * @param Mock|null $repositoryMock
     *
     * @return BookmarkService
     */
    protected function getBookmarkService(Mock $entityManagerMock = null, Mock $repositoryMock = null)
    {
        if (!$entityManagerMock) {
            $entityManagerMock = $this->getMockBuilder(EntityManager::class)
                ->disableOriginalConstructor()
                ->getMock();
        }

        if (!$repositoryMock) {
            $repositoryMock = $this->getMockBuilder(EntityRepository::class)
                ->disableOriginalConstructor()
                ->getMock();
        }

        return new BookmarkService($entityManagerMock, $repositoryMock);
    }
}