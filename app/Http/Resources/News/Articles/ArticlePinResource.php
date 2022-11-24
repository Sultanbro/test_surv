<?php

namespace App\Http\Resources\News\Articles;

use App\Models\News\Article;
use App\Repositories\Interfaces\News\ArticleRepositoryInterface;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

/**
 * @property Article $resource
 */
class ArticlePinResource extends JsonResource
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
            'is_pinned' => $this->repository->isPinned($this->resource, Auth::user()),
        ];
    }
}
