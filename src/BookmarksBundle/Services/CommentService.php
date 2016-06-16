<?php

namespace BookmarksBundle\Services;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use BookmarksBundle\Entity\Comment;

class CommentService
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var EntityRepository
     */
    private $repository;

    /**
     * BookmarkService constructor
     *
     * @param EntityManager $entityManager
     * @param EntityRepository $repository
     */
    public function __construct(EntityManager $entityManager, EntityRepository $repository)
    {
        $this->entityManager = $entityManager;
        $this->repository = $repository;
    }

    /**
     * Save comment object
     *
     * @param Comment $comment
     */
    public function save(Comment $comment)
    {
        $this->entityManager->persist($comment);
        $this->entityManager->flush();
    }

    /**
     * Update comment text
     *
     * @param Comment $comment
     * @param string $text
     */
    public function updateText(Comment $comment, $text)
    {
        if ($comment->isChangeableAndDeletable()) {
            $comment->setText($text);
            $this->entityManager->flush();
        }
    }

    /**
     * Delete comment
     *
     * @param Comment $comment
     */
    public function delete(Comment $comment)
    {
        if ($comment->isChangeableAndDeletable()) {
            $this->entityManager->remove($comment);
            $this->entityManager->flush();
        }
    }
}