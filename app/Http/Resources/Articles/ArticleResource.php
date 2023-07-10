<?php

namespace App\Http\Resources\Articles;

use App\Helpers\DateHelper;
use App\Http\Resources\Files\FileResource;
use App\Http\Resources\Likes\LikeResource;
use App\Http\Resources\Users\UserResource;
use App\Models\Article\Article;
use App\Repositories\Interfaces\Article\ArticleRepositoryInterface;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

/**
 * @property Article $resource
 */
class ArticleResource extends JsonResource
{
    protected ArticleRepositoryInterface $repository;

    public function __construct($resource)
    {
        parent::__construct($resource);

        $this->repository = app(ArticleRepositoryInterface::class);
    }

    public function toArray($request): array
    {
        return [
            'id' => $this->resource->id,
            'title' => $this->resource->title,
            'content' => $this->resource->content,
            'available_for' => $this->resource->available_for,
            'created_at' => $this->resource->created_at,
            'author' => (new UserResource($this->resource->author))->toArray($request),
            'likes' => LikeResource::collection($this->resource->likes)->toArray($request),
            'likes_count' => $this->repository->likesCount($this->resource),
            'comments_count' => $this->repository->commentsCount($this->resource),
            'is_liked' => $this->repository->likeExists($this->resource, Auth::id()),
            'is_favourite' => $this->repository->isFavourite($this->resource, Auth::user()),
            'is_pinned' => $this->repository->isPinned($this->resource, Auth::user()),
            'views_count' => $this->repository->viewsCount($this->resource),
            'files' => FileResource::collection($this->resource->files)->toArray($request),
        ];
    }
}
