<?php

namespace BookmarksBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use BookmarksBundle\Entity\Bookmark;

class RESTController extends Controller
{
    /**
     * @Route("/bookmarks")
     * @Method({"GET"})
     */
    public function latestBookmarksAction()
    {
        // todo: move to service
        $data = $this->getDoctrine()->getRepository(Bookmark::class)->findBy([], ['createdAt' => 'DESC'], 10);

        return $this->json($data);
    }

    /**
     * @Route("/bookmarks/{url}", requirements={"url" = ".+"})
     * @Method({"GET"})
     */
    public function getBookmarkByUrlAction(Bookmark $bookmark)
    {
        return $this->json($bookmark);
    }
}