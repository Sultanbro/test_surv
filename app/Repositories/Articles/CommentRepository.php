<?php

namespace App\Repositories\Articles;


use App\Entities\DataTransferObjects\News\CommentStoreDTO;
use App\Models\Article\Article;
use App\Models\Comment\Comment;
use App\Repositories\Interfaces\Article\CommentRepositoryInterface;

class CommentRepository implements CommentRepositoryInterface
{
    public function get($id, $articleId = null): Comment
    {
        return Comment::where([
            'id' => $id,
            'article_id' => $articleId,
        ])->firstOrFail();
    }

    public function store(CommentStoreDTO $dto): Comment
    {
        return Comment::create($dto->toArray());
    }

    public function delete($id): int
    {
        return Comment::where('id', $id)->delete();
    }

    public function forArticle(Article $article)
    {
        return $article->comments()
            ->orderBy('created_at')
            ->with([
                'likes' => fn($q) => $q->with('user'),
                'reactions' => fn($q) => $q->with('user'),
            ])
            ->get()
            ->toTree();
    }

    public function reactionExists($id, $userId, $reaction): bool
    {
        return Comment::where('id', $id)
            ->whereHas('reactions', function ($q) use ($userId, $reaction) {
                $q->where([
                    'user_id' => $userId,
                    'reaction' => $reaction
                ]);
            })->exists();
    }

    public function likeExists(Comment $comment, $userId): bool
    {
        return $comment->likes()->where('user_id', $userId)->exists();
    }

    public function likesCount(Comment $comment): int
    {
        return $comment->likes()->count();
    }
}
