<?php

namespace App\Repositories\Poll;


use App\Entities\DataTransferObjects\News\ArticleStoreDTO;
use App\Models\Article\Article;
use App\Models\File\File;
use App\User;

class PollRepository
{
    public function store(ArticleStoreDTO $dto): Article
    {
        $article = Article::create($dto->toArray());

        $article->files()->saveMany(File::whereIn('id', $dto->getFiles())->get());

        return $article;
    }

    public function update(Article $article, ArticleStoreDTO $dto): Article
    {
        $article->update($dto->toArray());

        $article->files()->whereNotIn(
            'id',
            $dto->getFiles(),
        )->delete();

        $article->files()->saveMany(File::whereIn('id', $dto->getFiles())->get());

        return $article;
    }

    public function delete($id)
    {
        return Article::where('id', $id)->delete();
    }

    public function likeExists(Article $article, $userId): bool
    {
        return $article->likes()->where('user_id', $userId)->exists();
    }

    public function likesCount(Article $article): int
    {
        return $article->likes()->count();
    }

    public function viewsCount(Article $article): int
    {
        return $article->views()->count();
    }

    public function commentsCount(Article $article): int
    {
        return $article->comments()->count();
    }

    public function isFavourite(Article $article, User $user): bool
    {
        return $user->favouriteArticles()->where('article_id', $article->id)->exists();
    }

    public function isPinned(Article $article, User $user): bool
    {
        return $user->pinnedArticles()->where('article_id', $article->id)->exists();
    }
}
