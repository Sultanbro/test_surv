<?php

namespace App\Http\Resources\Articles\Comments;


//use App\ArticleLib\Http\Resources\Reactions\ReactionResource;
use App\Helpers\DateHelper;
use App\Http\Resources\Likes\LikeResource;
use App\Http\Resources\Reactions\ReactionCollection;
use App\Http\Resources\Users\UserResource;
use App\Models\Comment\Comment;
use App\Repositories\Interfaces\Article\CommentRepositoryInterface;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

/**
 * @property Comment $resource
 */
class ArticleCommentResource extends JsonResource
{
    protected CommentRepositoryInterface $repository;

    public function __construct($resource)
    {
        parent::__construct($resource);

        $this->repository = app(CommentRepositoryInterface::class);
    }

    public function toArray($request): array
    {
        return array_merge(
            [
                'id' => $this->resource->id,
                'content' => $this->resource->content,
                'author' => (new UserResource($this->resource->author))->toArray($request),
                'comments' => ArticleCommentResource::collection(
                    $this->resource->children()->orderBy('created_at')->get()
                )->toArray($request),
                'likes' => LikeResource::collection($this->resource->likes)->toArray($request),
                'likes_count' => $this->repository->likesCount($this->resource),
                'is_liked' => $this->repository->likeExists($this->resource, Auth::id()),
                'created_at' => DateHelper::format($this->resource->created_at),
            ],
            (new ReactionCollection($this->resource->reactions))->toArray($request)
        );
    }
}
