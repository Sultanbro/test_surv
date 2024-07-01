<?php

namespace App\Service\Article\Comment;

use App\Entities\DataTransferObjects\News\CommentStoreDTO;
use App\Exceptions\News\BusinessLogicException;
use App\Models\Article\Article;
use App\Models\Comment\Comment;
use App\Repositories\Interfaces\Article\CommentRepositoryInterface;
use Illuminate\Support\Facades\Auth;

class CommentService
{
    public function __construct(protected CommentRepositoryInterface $repository)
    {
    }

    public function index(Article $article)
    {
        return $this->repository->forArticle($article);
    }

    /**
     * @param CommentStoreDTO $dto
     * @return Comment
     * @throws BusinessLogicException
     */
    public function store(CommentStoreDTO $dto): Comment
    {
        if (!$model = $this->repository->store($dto)) {
            throw new BusinessLogicException(__('exception.save'));
        }

        return $model;
    }

    /**
     * @param Comment $comment
     * @return int
     * @throws BusinessLogicException
     */
    public function delete(Comment $comment): int
    {
        if ($comment->user_id !== Auth::id()) {
            throw new BusinessLogicException(__('exception.no_access'));
        }

        if (!$result = $this->repository->delete($comment->id)) {
            throw new BusinessLogicException(__('exception.delete'));
        }

        $result += $comment->children()->delete();

        return $result;
    }
}
