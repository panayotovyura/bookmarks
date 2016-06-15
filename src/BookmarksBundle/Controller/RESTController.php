<?php

namespace BookmarksBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use BookmarksBundle\Entity\Bookmark;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints as Assert;

class RESTController extends Controller
{
    /**
     * @Route("/bookmark")
     * @Method({"GET"})
     */
    public function latestBookmarksAction()
    {
        // todo: move to service
        $data = $this->getDoctrine()->getRepository(Bookmark::class)->findBy([], ['createdAt' => 'DESC'], 10);

        return $this->json($data);
    }

    /**
     * @Route("/bookmark/{url}", requirements={"url" = ".+"})
     * @Method({"GET"})
     */
    public function getBookmarkByUrlAction(Bookmark $bookmark)
    {
        return $this->json($bookmark);
    }

    /**
     * @Route("/bookmark")
     * @Method({"POST"})
     */
    public function createBookmarkByUrlAction(Request $request)
    {
        $url = $request->get('url');
        $violationsList = $this->get('validator')->validate(
            $url,
            [
                new Assert\NotBlank(),
                new Assert\Url()
            ]
        );

        if (count($violationsList) > 0) {
            throw new BadRequestHttpException($violationsList->get(0)->getMessage());
        }

        $responseStatus = Response::HTTP_OK;
        // todo: move to service
        $bookmark = $this->getDoctrine()->getRepository(Bookmark::class)->findOneBy(['url' => $url]);

        if (!($bookmark instanceof Bookmark)) {
            // todo: move to service
            $bookmark = (new Bookmark())->setUrl($url);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($bookmark);
            $entityManager->flush();
            $responseStatus = Response::HTTP_CREATED;
        }

        return $this->json(['uid' => $bookmark->getUid()], $responseStatus);
    }
}