<?php

namespace App\Repositories\Interfaces\News;


use App\Entities\DataTransferObjects\News\CommentStoreDTO;
use App\Models\News\Article;
use App\Models\News\Comment;

interface CommentRepositoryInterface
{
    public function get($id, $articleId);

    public function store(CommentStoreDTO $dto): Comment;

    public function delete($id): int;

    public function forArticle(Article $article);

    public function likeExists(Comment $comment, $userId): bool;

    public function likesCount(Comment $comment): int;

    public function reactionExists($id, $userId, $reaction): bool;
}
