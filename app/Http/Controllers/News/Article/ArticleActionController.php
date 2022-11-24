<?php

namespace App\Http\Controllers\News\Article;

use App\Http\Controllers\Controller;
use App\Http\Requests\News\ArticleRequest;
use App\Http\Resources\Articles\News\ArticleViewResource;
use App\Http\Resources\News\Articles\ArticleFavouriteResource;
use App\Http\Resources\News\Articles\ArticleLikeResource;
use App\Http\Resources\News\Articles\ArticlePinResource;
use App\Http\Resources\Responses\JsonSuccessResponse;
use App\Service\News\ArticleActionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class ArticleActionController extends Controller
{
    public function __construct(protected ArticleActionService $service)
    {
    }

    public function like(ArticleRequest $request): JsonResponse
    {
        $article = $request->getArticle();

        $this->service->like($article, Auth::id());

        return response()->json(
            new JsonSuccessResponse(
                __('model/article.like'),
                (new ArticleLikeResource($article))->toArray($request)
            )
        );
    }

    public function favourite(ArticleRequest $request): JsonResponse
    {
        $article = $request->getArticle();

        $this->service->favourite($article, Auth::user());

        return response()->json(
            new JsonSuccessResponse(
                __('model/article.favourite'),
                (new ArticleFavouriteResource($article))->toArray($request)
            )
        );
    }

    public function pin(ArticleRequest $request): JsonResponse
    {
        $article = $request->getArticle();

        $this->service->pin($article, Auth::user());

        return response()->json(
            new JsonSuccessResponse(
                __('model/article.pin'),
                (new ArticlePinResource($article))->toArray($request)
            )
        );
    }

    public function views(ArticleRequest $request): JsonResponse
    {
        $article = $request->getArticle();
        $this->service->views($article, Auth::user());

        return response()->json(
            new JsonSuccessResponse(
                __('model/article.view'),
                (new ArticleViewResource($article))->toArray($request)
            )
        );
    }
}
