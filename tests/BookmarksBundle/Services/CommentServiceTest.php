<?php

namespace Tests\BookmarksBundle\Services;

use BookmarksBundle\Services\CommentService;
use Doctrine\ORM\EntityManager;
use \PHPUnit_Framework_MockObject_MockObject as Mock;
use BookmarksBundle\Entity\Comment;

class CommentServiceTest extends \PHPUnit_Framework_TestCase
{
    public function testSave()
    {
        $entityManagerMock = $this->getMockBuilder(EntityManager::class)
            ->disableOriginalConstructor()
            ->getMock();

        $comment = new Comment();
        $entityManagerMock->expects($this->once())
            ->method('persist')
            ->with($comment)
            ->willReturn(null);

        $entityManagerMock->expects($this->once())
            ->method('flush')
            ->willReturn(null);

        $commentService = $this->getCommentService($entityManagerMock);

        $commentService->save($comment);
    }

    public function testUpdateText()
    {
        $entityManagerMock = $this->getMockBuilder(EntityManager::class)
            ->disableOriginalConstructor()
            ->getMock();

        $entityManagerMock->expects($this->once())
            ->method('flush')
            ->willReturn(null);

        $commentService = $this->getCommentService($entityManagerMock);

        $comment = (new Comment())->setCreatedAt(new \DateTime('-10 minutes'));

        $commentService->updateText($comment, 'some text');
    }

    public function testNotUpdateText()
    {
        $entityManagerMock = $this->getMockBuilder(EntityManager::class)
            ->disableOriginalConstructor()
            ->getMock();

        $entityManagerMock->expects($this->never())
            ->method('flush');

        $commentService = $this->getCommentService($entityManagerMock);

        $comment = (new Comment())->setCreatedAt(new \DateTime('-2 hours'));

        $commentService->updateText($comment, 'some text');
    }

    public function testDeleteText()
    {
        $entityManagerMock = $this->getMockBuilder(EntityManager::class)
            ->disableOriginalConstructor()
            ->getMock();

        $entityManagerMock->expects($this->once())
            ->method('remove')
            ->willReturn(null);

        $entityManagerMock->expects($this->once())
            ->method('flush')
            ->willReturn(null);

        $commentService = $this->getCommentService($entityManagerMock);

        $comment = (new Comment())->setCreatedAt(new \DateTime('-10 minutes'));

        $commentService->delete($comment);
    }

    public function testNotDeleteText()
    {
        $entityManagerMock = $this->getMockBuilder(EntityManager::class)
            ->disableOriginalConstructor()
            ->getMock();

        $entityManagerMock->expects($this->never())
            ->method('remove')
            ->willReturn(null);

        $entityManagerMock->expects($this->never())
            ->method('flush');

        $commentService = $this->getCommentService($entityManagerMock);

        $comment = (new Comment())->setCreatedAt(new \DateTime('-2 hours'));

        $commentService->delete($comment);
    }

    /**
     * @param Mock|null $entityManagerMock
     *
     * @return CommentService
     */
    protected function getCommentService(Mock $entityManagerMock = null)
    {
        if (!$entityManagerMock) {
            $entityManagerMock = $this->getMockBuilder(EntityManager::class)
                ->disableOriginalConstructor()
                ->getMock();
        }

        return new CommentService($entityManagerMock);
    }
}