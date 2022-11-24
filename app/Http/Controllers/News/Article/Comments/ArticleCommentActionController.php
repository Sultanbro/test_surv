<?php

namespace App\Http\Controllers\News\Article\Comments;

use App\Http\Controllers\Controller;
use App\Http\Requests\News\Comments\ArticleCommentReactionRequest;
use App\Http\Requests\News\Comments\ArticleCommentRequest;
use App\Http\Resources\News\Articles\Comments\ArticleCommentLikeResource;
use App\Http\Resources\News\Reactions\ReactionCollection;
use App\Http\Resources\Responses\JsonSuccessResponse;
use App\Service\News\Comments\CommentActionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class ArticleCommentActionController extends Controller
{
    public function __construct(protected CommentActionService $service)
    {
    }

    public function like(ArticleCommentRequest $request): JsonResponse
    {
        $comment = $this->service->like($request->getComment(), Auth::id());

        return response()->json(
            new JsonSuccessResponse(
                __('model/comment.like'),
                (new ArticleCommentLikeResource($comment))->toArray($request)
            )
        );
    }

    public function reaction(ArticleCommentReactionRequest $request): JsonResponse
    {
        $comment = $this->service->reaction($request->getComment(), $request->getReaction(), Auth::id());

        return response()->json(
            new JsonSuccessResponse(
                __('model/comment.reaction'),
                (new ReactionCollection($comment->reactions))->toArray($request),
            )
        );
    }
}
