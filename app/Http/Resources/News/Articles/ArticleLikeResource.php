<?php

namespace App\Http\Resources\News\Articles;

use App\Models\News\Article;
use App\Repositories\Interfaces\News\ArticleRepositoryInterface;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

/**
 * @property Article $resource
 */
class ArticleLikeResource extends JsonResource
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
            'is_liked' => $this->repository->likeExists($this->resource, Auth::id()),
            'likes_count' => $this->repository->likesCount($this->resource),
        ];
    }
}
