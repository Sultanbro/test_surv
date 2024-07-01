<?php

namespace App\Entities\DataTransferObjects\News;

class ArticlePinnedDTO
{
    public function __construct(
        protected array $pagination,
        protected array $articles,
        protected array $pinned
    )
    {
    }

    public function toArray(): array
    {
        return [
            'pagination' => $this->pagination,
            'articles' => $this->articles,
            'pinned_articles' => $this->pinned
        ];
    }
}
