<?php

namespace App\Http\Controllers\News\Article\Comments;

use App\Exceptions\News\BusinessLogicException;
use App\Http\Controllers\Controller;
use App\Http\Requests\News\ArticleRequest;
use App\Http\Requests\News\Comments\ArticleCommentIndexRequest;
use App\Http\Requests\News\Comments\ArticleCommentRequest;
use App\Http\Requests\News\Comments\ArticleCommentStoreRequest;
use App\Http\Resources\News\Articles\Comments\ArticleCommentCollection;
use App\Http\Resources\News\Articles\Comments\ArticleCommentResource;
use App\Http\Resources\Responses\JsonSuccessResponse;
use App\Service\News\Comments\CommentService;
use Illuminate\Http\JsonResponse;

class ArticleCommentController extends Controller
{
    // TODO: delete test page
    public function create(ArticleRequest $request)
    {
        return view('test.comment-create', ['articleId' => $request->getArticleId()]);
    }

    public function __construct(protected CommentService $service)
    {
    }

    public function index(ArticleCommentIndexRequest $request): JsonResponse
    {
        $comments = $this->service->index($request->getArticle());

        return response()->json(
            new JsonSuccessResponse(
                __('model/comment.index'),
                (new ArticleCommentCollection($comments))->toArray($request)
            )
        );
    }

    /**
     * @param ArticleCommentStoreRequest $request
     * @return JsonResponse
     * @throws BusinessLogicException
     */
    public function store(ArticleCommentStoreRequest $request): JsonResponse
    {
        $comment = $this->service->store($request->getData());

        return response()->json(
            new JsonSuccessResponse(
                __('model/comment.store'),
                (new ArticleCommentResource($comment))->toArray($request)
            )
        );
    }

    /**
     * @param ArticleCommentRequest $request
     * @return JsonResponse
     * @throws BusinessLogicException
     */
    public function delete(ArticleCommentRequest $request): JsonResponse
    {
        $this->service->delete($request->getComment());

        return response()->json(
            new JsonSuccessResponse(
                __('model/comment.delete')
            )
        );
    }
}
