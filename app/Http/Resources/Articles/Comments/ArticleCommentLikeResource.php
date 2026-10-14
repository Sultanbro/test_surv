<?php

namespace App\Http\Resources\Articles\Comments;

use App\Models\Comment\Comment;
use App\Repositories\Interfaces\Article\CommentRepositoryInterface;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

/**
 * @property Comment $resource
 */
class ArticleCommentLikeResource extends JsonResource
{
    protected CommentRepositoryInterface $repository;

    public function __construct($resource)
    {
        parent::__construct($resource);

        $this->repository = app(CommentRepositoryInterface::class);
    }

    public function toArray($request): array
    {
        return [
            'id' => $this->resource->id,
            'is_liked' => $this->repository->likeExists($this->resource, Auth::id()),
            'likes_count' => $this->repository->likesCount($this->resource),
        ];
    }
}
