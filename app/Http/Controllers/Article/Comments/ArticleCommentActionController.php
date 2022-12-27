<?php

namespace App\Http\Controllers\Article\Comments;

use App\Http\Controllers\Controller;
use App\Http\Requests\Article\Comments\ArticleCommentReactionRequest;
use App\Http\Requests\Article\Comments\ArticleCommentRequest;
use App\Http\Resources\Articles\Comments\ArticleCommentLikeResource;
use App\Http\Resources\Reactions\ReactionCollection;
use App\Http\Resources\Responses\JsonSuccessResponse;
use App\Service\Article\Comment\CommentActionService;
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
