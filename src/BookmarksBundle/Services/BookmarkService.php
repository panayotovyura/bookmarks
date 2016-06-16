<?php

namespace BookmarksBundle\Services;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use BookmarksBundle\Entity\Bookmark;

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

    /**
     * Get bookmark by url
     *
     * @param $url
     *
     * @return null|Bookmark
     */
    public function getByUrl($url)
    {
        return $this->repository->findOneBy(['url' => $url]);
    }

    /**
     * Save bookmark object
     *
     * @param Bookmark $bookmark
     */
    public function save(Bookmark $bookmark)
    {
        $this->entityManager->persist($bookmark);
        $this->entityManager->flush();
    }
}