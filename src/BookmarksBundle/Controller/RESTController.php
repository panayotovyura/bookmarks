<?php

namespace BookmarksBundle\Controller;

use BookmarksBundle\Entity\Comment;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
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
     * Get 10 latest bookmarks action
     *
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
     * Get bookmark by url action
     *
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
     * Create bookmark action
     *
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
        $requestData = $this->get('serializer')->decode($request->getContent(), 'json');
        $url = isset($requestData['url']) ? $requestData['url'] : null;

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
     * Create comment for bookmark action
     *
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
        $requestData = $this->get('serializer')->decode($request->getContent(), 'json');
        $text = isset($requestData['text']) ? $requestData['text'] : null;

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
     * Update comment action
     *
     * @Route("/comment/{uid}")
     * @Method({"PUT"})
     *
     * @param Comment $comment
     * @param Request $request
     *
     * @throws NotFoundHttpException
     * @throws BadRequestHttpException
     *
     * @return JsonResponse
     */
    public function updateCommentAction(Comment $comment, Request $request)
    {
        if ($comment->getIp() != $request->getClientIp()) {
            throw new NotFoundHttpException();
        }

        $requestData = $this->get('serializer')->decode($request->getContent(), 'json');
        $text = isset($requestData['text']) ? $requestData['text'] : null;

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
        if ($comment->isChangeableAndDeletable()) {
            $comment->setText($text);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->flush();
        }

        return $this->json(['uid' => $comment->getUid()]);
    }

    /**
     * Delete comment action
     *
     * @Route("/comment/{uid}")
     * @Method({"DELETE"})
     *
     * @param Comment $comment
     * @param Request $request
     *
     * @throws NotFoundHttpException
     *
     * @return JsonResponse
     */
    public function deleteCommentAction(Comment $comment, Request $request)
    {
        if ($comment->getIp() != $request->getClientIp()) {
            throw new NotFoundHttpException();
        }

        // todo: move to service
        if ($comment->isChangeableAndDeletable()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($comment);
            $entityManager->flush();
        }

        return $this->json([], Response::HTTP_NO_CONTENT);
    }
}