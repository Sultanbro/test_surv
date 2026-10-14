<?php

namespace App\Service\Article;


use App\Models\Article\Article;
use App\Repositories\Interfaces\Article\ArticleRepositoryInterface;
use App\User;

class ArticleActionService
{
    public function __construct(protected ArticleRepositoryInterface $repository)
    {
    }

    public function like(Article $article, $userId): Article
    {
        $this->repository->likeExists($article, $userId)
            ? $article->likes()->where($this->getUserId($userId))->delete()
            : $article->likes()->create($this->getUserId($userId));

        return $article;
    }

    public function favourite(Article $article, User $user): Article
    {
        $user->favouriteArticles()->toggle($article->id);

        return $article;
    }

    public function pin(Article $article, User $user): Article
    {
        $user->pinnedArticles()->toggle($article->id);

        return $article;
    }

    public function views(Article $article, User $user): Article
    {
        $article->views()->syncWithoutDetaching([$user->id]);

        return $article;
    }

    protected function getUserId(int $userId): array
    {
        return ['user_id' => $userId];
    }
}
