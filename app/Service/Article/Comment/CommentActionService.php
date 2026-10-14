<?php

namespace App\Service\Article\Comment;


use App\Models\Comment\Comment;
use App\Repositories\Interfaces\Article\CommentRepositoryInterface;

class CommentActionService
{
    public function __construct(protected CommentRepositoryInterface $repository)
    {
    }

    public function like(Comment $comment, int $userId): Comment
    {
        $this->repository->likeExists($comment, $userId)
            ? $comment->likes()->where($this->getUserId($userId))->delete()
            : $comment->likes()->create($this->getUserId($userId));

        return $comment;
    }

    public function reaction(Comment $comment, string $reaction, int $userId): Comment
    {
        $this->repository->reactionExists($comment->id, $userId, $reaction)
            ? $comment->reactions()->where(array_merge($this->getUserId($userId), $this->getReaction($reaction)))->delete()
            : $comment->reactions()->updateOrCreate($this->getUserId($userId), $this->getReaction($reaction));

        return $comment;
    }

    protected function getUserId(int $userId): array
    {
        return ['user_id' => $userId];
    }

    protected function getReaction(string $reaction): array
    {
        return ['reaction' => $reaction];
    }
}
