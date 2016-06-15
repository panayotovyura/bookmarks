<?php

namespace BookmarksBundle\Controller;

use BookmarksBundle\Entity\Comment;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use BookmarksBundle\Entity\Bookmark;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Validator\Constraints as Assert;

class RESTController extends Controller
{
    /**
     * @Route("/bookmark")
     * @Method({"GET"})
     *
     * @return JsonResponse
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
     *
     * @param Bookmark $bookmark
     *
     * @return JsonResponse
     */
    public function getBookmarkByUrlAction(Bookmark $bookmark)
    {
        return $this->json($bookmark);
    }

    /**
     * @Route("/bookmark")
     * @Method({"POST"})
     *
     * @param Request $request
     *
     * @throws BadRequestHttpException
     *
     * @return JsonResponse
     */
    public function createBookmarkAction(Request $request)
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

    /**
     * @Route("/bookmark/{uid}/comment")
     * @Method({"POST"})
     *
     * @param Bookmark $bookmark
     * @param Request $request
     *
     * @throws BadRequestHttpException
     *
     * @return JsonResponse
     */
    public function createComment(Bookmark $bookmark, Request $request)
    {
        $text = $request->get('text');
        // todo: move to function
        $violationsList = $this->get('validator')->validate(
            $text,
            [
                new Assert\NotBlank(),
            ]
        );

        if (count($violationsList) > 0) {
            throw new BadRequestHttpException($violationsList->get(0)->getMessage());
        }

        $comment = (new Comment())
            ->setIp($request->getClientIp())
            ->setText($text)
            ->setBookmark($bookmark);

        // todo: move to service
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($comment);
        $entityManager->flush();

        return $this->json(['uid' => $comment->getUid()], Response::HTTP_CREATED);
    }

    /**
     * @Route("/comment/{uid}")
     * @Method({"PUT"})
     */
    public function updateCommentAction(Comment $comment, Request $request)
    {
        if ($comment->getIp() != $request->getClientIp()) {
            throw new NotFoundHttpException();
        }

        $putStr = $request->getContent();
        parse_str($putStr, $putData);
        $text = isset($putData['text']) ? $putData['text'] : null;
        // todo: move to function
        $violationsList = $this->get('validator')->validate(
            $text,
            [
                new Assert\NotBlank(),
            ]
        );

        if (count($violationsList) > 0) {
            throw new BadRequestHttpException($violationsList->get(0)->getMessage());
        }

        // todo: move to service
        if ($comment->getCreatedAt()->add(new \DateInterval('PT1H')) > new \DateTime()) {
            $comment->setText($text);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->flush();
        }

        return $this->json(['uid' => $comment->getUid()]);

    }
}