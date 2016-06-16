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
use Symfony\Component\Validator\ConstraintViolationListInterface;

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
        return $this->json(
            $this->get('bookmarks')->getLatest()
        );
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
        $url = $this->getRequestBodyJsonVariable('url', $request);

        $violationsList = $this->validateUrl($url);
        if (count($violationsList) > 0) {
            throw new BadRequestHttpException($violationsList->get(0)->getMessage());
        }

        $bookmark = $this->get('bookmarks')->getByUrl($url);
        $responseStatus = Response::HTTP_OK;

        if (!($bookmark instanceof Bookmark)) {
            $bookmark = (new Bookmark())->setUrl($url);
            $this->get('bookmarks')->save($bookmark);
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
        $text = $this->getRequestBodyJsonVariable('text', $request);

        $violationsList = $this->validateText($text);
        if (count($violationsList) > 0) {
            throw new BadRequestHttpException($violationsList->get(0)->getMessage());
        }

        $comment = (new Comment())
            ->setIp($request->getClientIp())
            ->setText($text)
            ->setBookmark($bookmark);

        $this->get('comment')->save($comment);

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

        $text = $this->getRequestBodyJsonVariable('text', $request);

        $violationsList = $this->validateText($text);
        if (count($violationsList) > 0) {
            throw new BadRequestHttpException($violationsList->get(0)->getMessage());
        }

        $this->get('comment')->updateText($comment, $text);

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

        $this->get('comment')->delete($comment);

        return $this->json([], Response::HTTP_NO_CONTENT);
    }

    /**
     * Validate text, returns constraint violations list
     *
     * @param $text
     *
     * @return ConstraintViolationListInterface
     */
    protected function validateText($text)
    {
        return $this->get('validator')->validate(
            $text,
            [
                new Assert\NotBlank(),
            ]
        );
    }

    /**
     * Validate url, returns constraint violations list
     *
     * @param $url
     *
     * @return ConstraintViolationListInterface
     */
    protected function validateUrl($url)
    {
        return $this->get('validator')->validate(
            $url,
            [
                new Assert\NotBlank(),
                new Assert\Url()
            ]
        );
    }

    /**
     * Get variable from json request body
     *
     * @param string $name
     * @param Request $request
     *
     * @return mixed
     */
    protected function getRequestBodyJsonVariable($name, Request $request)
    {
        $requestData = $this->get('serializer')->decode($request->getContent(), 'json');
        return isset($requestData[$name]) ? $requestData[$name] : null;
    }
}
