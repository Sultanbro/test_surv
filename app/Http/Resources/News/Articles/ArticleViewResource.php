<?php

namespace App\Http\Resources\Articles\News;

use App\Models\News\Article;
use App\Repositories\Interfaces\News\ArticleRepositoryInterface;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property Article $resource
 */
class ArticleViewResource extends JsonResource
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
            'views_count' => $this->repository->viewsCount($this->resource),
        ];
    }
}
