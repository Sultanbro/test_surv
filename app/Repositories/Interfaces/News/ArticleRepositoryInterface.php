<?php

namespace App\Repositories\Interfaces\News;


use App\Entities\DataTransferObjects\News\ArticleStoreDTO;
use App\Models\News\Article;
use App\User;

interface ArticleRepositoryInterface
{
    public function store(ArticleStoreDTO $dto): Article;

    public function update(Article $article, ArticleStoreDTO $dto): Article;

    public function delete($id);

    public function likeExists(Article $article, $userId): bool;

    public function likesCount(Article $article): int;

    public function viewsCount(Article $article): int;

    public function commentsCount(Article $article): int;

    public function isFavourite(Article $article, User $user): bool;

    public function isPinned(Article $article, User $user): bool;
}
