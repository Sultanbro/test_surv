<?php

namespace App\Repositories\Interfaces\Article;


use App\Entities\DataTransferObjects\News\CommentStoreDTO;
use App\Models\Article\Article;
use App\Models\Comment\Comment;

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
