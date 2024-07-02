<?php

namespace App\Http\Requests\Article\Comments;


use App\Http\Requests\Article\ArticleRequest;
use App\Models\Comment\Comment;
use App\Repositories\Interfaces\Article\CommentRepositoryInterface;

class ArticleCommentRequest extends ArticleRequest
{
    protected CommentRepositoryInterface $repository;

    public function __construct(array $query = [], array $request = [], array $attributes = [], array $cookies = [], array $files = [], array $server = [], $content = null)
    {
        parent::__construct($query, $request, $attributes, $cookies, $files, $server, $content);

        $this->repository = app(CommentRepositoryInterface::class);
    }

    public function getComment(): Comment
    {
        return $this->repository->get($this->getCommentId(), $this->getArticleId());
    }

    public function getCommentId(): ?string
    {
        return $this->route('comment_id');
    }
}
