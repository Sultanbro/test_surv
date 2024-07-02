<?php

namespace App\Http\Resources\Articles;


use App\Http\Resources\Pagination\PaginationCollection;

class ArticleCollection extends PaginationCollection
{
    public function __construct($resource)
    {
        parent::__construct($resource);

        $this->items = array(
            'articles' => ArticleResource::collection($this->resource),
        );
    }
}
