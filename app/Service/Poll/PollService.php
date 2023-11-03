<?php

namespace App\Service\Poll;


use App\Entities\DataTransferObjects\News\ArticleStoreDTO;
use App\Exceptions\News\BusinessLogicException;
use App\Models\Article\Article;
use App\Repositories\Interfaces\Article\ArticleRepositoryInterface;
use App\Repositories\Poll\PollRepository;

class PollService
{
    public function __construct(protected PollRepository $repository)
    {
    }

    /**
     * @param ArticleStoreDTO $dto
     * @return Article
     * @throws BusinessLogicException
     */
    public function store(ArticleStoreDTO $dto): Article
    {
        if (!$article = $this->repository->store($dto)) {
            throw new BusinessLogicException(__('exception.save'));
        }

        return $article;
    }

    /**
     * @param Article $article
     * @param ArticleStoreDTO $dto
     * @return Article
     * @throws BusinessLogicException
     */
    public function update(Article $article, ArticleStoreDTO $dto): Article
    {
        if (!$this->repository->update($article, $dto)) {
            throw new BusinessLogicException(__('exception.save'));
        }

        return $article;
    }

    /**
     * @param Article $article
     * @param int $userId
     * @return bool
     * @throws BusinessLogicException
     */
    public function delete(Article $article, int $userId): bool
    {
        if ($article->author_id !== $userId) {
            throw new BusinessLogicException(__('exception.no_access'));
        }

        if (!$result = $this->repository->delete($article->id)) {
            throw new BusinessLogicException(__('exception.delete'));
        }

        return $result;
    }
}
