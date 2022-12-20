<?php

namespace App\Http\Controllers\Article;

use App\Entities\DataTransferObjects\News\ArticlePinnedDTO;
use App\Exceptions\News\BusinessLogicException;
use App\Filters\Articles\ArticleFilter;
use App\Http\Controllers\Controller;
use App\Http\Requests\Article\ArticleIndexRequest;
use App\Http\Requests\Article\ArticleRequest;
use App\Http\Requests\Article\ArticleStoreRequest;
use App\Http\Resources\Articles\ArticleResource;
use App\Http\Resources\Pagination\PaginationResource;
use App\Http\Resources\Responses\JsonSuccessResponse;
use App\Models\Article\Article;
use App\Service\Article\ArticleService;
use App\Service\PaginationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class ArticleController extends Controller
{
    public function __construct(protected ArticleService $service)
    {
    }

    public function index(ArticleIndexRequest $request, ArticleFilter $filter, PaginationService $paginationService): JsonResponse
    {
        $user = Auth::user();

        $articles = Article::availableFor($user)->filter($filter)->orderByDesc('created_at');

        $pinArticles = (clone $articles)
            ->whereHas('pins', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            })
            ->get();

        $articles->whereNotIn('id', $pinArticles->pluck('id')->toArray());

        $paginationArticles = $paginationService->paginate($articles, $request->getPagination());

        return response()->json(new JsonSuccessResponse(
            __('model/article.index'),
            (new ArticlePinnedDTO(
                (new PaginationResource($paginationArticles))->toArray($request),
                ArticleResource::collection($paginationArticles)->toArray($request),
                ArticleResource::collection($pinArticles)->toArray($request),
            ))->toArray()
        ));
    }

    public function show(ArticleRequest $request): JsonResponse
    {
        return response()->json(
            new JsonSuccessResponse(
                __('model/article.show'),
                (new ArticleResource($request->getArticle()))->toArray($request)
            )
        );
    }

    /**
     * @param ArticleStoreRequest $request
     * @return JsonResponse
     * @throws BusinessLogicException
     */
    public function store(ArticleStoreRequest $request): JsonResponse
    {
        $article = $this->service->store($request->getData());

        return response()->json(
            new JsonSuccessResponse(
                __('model/article.store'),
                (new ArticleResource($article))->toArray($request)
            )
        );
    }

    /**
     * @param ArticleStoreRequest $request
     * @return JsonResponse
     * @throws BusinessLogicException
     */
    public function update(ArticleStoreRequest $request): JsonResponse
    {
        $article = $this->service->update(
            $request->getArticle(),
            $request->getData()
        );

        return response()->json(
            new JsonSuccessResponse(
                __('model/article.update'),
                (new ArticleResource($article))->toArray($request)
            )
        );
    }

    /**
     * @param ArticleRequest $request
     * @return JsonResponse
     * @throws BusinessLogicException
     */
    public function delete(ArticleRequest $request): JsonResponse
    {
        $this->service->delete($request->getArticle(), Auth::id());

        return response()->json(
            new JsonSuccessResponse(
                __('model/article.delete'),
            )
        );
    }
}
