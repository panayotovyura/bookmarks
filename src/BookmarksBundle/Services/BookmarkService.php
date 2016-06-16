<?php

namespace BookmarksBundle\Services;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;

class BookmarkService
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
     * Get latest bookmarks
     *
     * @param int $limit
     *
     * @return array
     */
    public function getLatest($limit = 10)
    {
        return $this->repository->findBy([], ['createdAt' => 'DESC'], $limit);
    }
}