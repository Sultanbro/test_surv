<?php

namespace App\Entities\DataTransferObjects\News;

use Illuminate\Contracts\Support\Arrayable;

class CommentStoreDTO implements Arrayable
{
    /**
     * @param string $content
     * @param int|null $parentId
     * @param int $articleId
     * @param int $userId
     */
    public function __construct(
        protected string $content,
        protected ?int   $parentId,
        protected int    $articleId,
        protected int    $userId,
    )
    {
    }

    public function toArray(): array
    {
        return [
            'content' => $this->content,
            'parent_id' => $this->parentId,
            'article_id' => $this->articleId,
            'user_id' => $this->userId
        ];
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function getParentId(): ?int
    {
        return $this->parentId;
    }

    public function getArticleId(): int
    {
        return $this->articleId;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }
}
