<?php

namespace BookmarksBundle\Services;

use Doctrine\ORM\EntityManager;
use BookmarksBundle\Entity\Comment;

class CommentService
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * BookmarkService constructor
     *
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
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
