<?php

namespace App\Http\Controllers\Article;

use App\Entities\DataTransferObjects\News\ArticlePinnedDTO;
use App\Exceptions\News\BusinessLogicException;
use App\Filters\Articles\ArticleFilter;
use App\Http\Controllers\Controller;
use App\Http\Requests\Article\ArticleIndexRequest;
use App\Http\Requests\Article\ArticleRequest;
use App\Http\Requests\Article\ArticleStoreRequest;
use App\Http\Requests\Article\ArticleVoteRequest;
use App\Http\Resources\Articles\ArticleResource;
use App\Http\Resources\Pagination\PaginationResource;
use App\Http\Resources\Responses\JsonSuccessResponse;
use App\Models\Article\Article;
use App\Models\Article\PollVote;
use App\Service\Article\ArticleService;
use App\Service\PaginationService;
use App\User;
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

        $articles = Article::with("views")
            ->with(["questions" => fn($query) => $query->with(['answers' => fn($query) => $query->with("votes")])])
            ->availableFor($user)->filter($filter)
            ->where('created_at', '>', $user->created_at)
            ->orderByDesc('created_at');

        $pinArticles = (clone $articles)
            ->whereHas('pins', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            })
            ->get();

        $articles->whereNotIn('id', $pinArticles->pluck('id')->toArray());

        $paginationArticles = $paginationService->paginate($articles, $request->getPagination());

        //if($user->id == 4444) $paginationArticles = $this->service->parseImages($paginationArticles);

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

    /**
     * @return JsonResponse
     */
    public function countUnviewed(): JsonResponse
    {
        $userId = Auth::user()->id;

        $count = Article::countUnviewed($userId);

        return response()->json(['count' => $count]);
    }

    /**
     * @param ArticleVoteRequest $request
     * @return JsonResponse
     */
    public function voteForArticle(ArticleVoteRequest $request): JsonResponse
    {
        $data = $request->validated();
        $article = $request->getArticle();
        /** @var User $user */
        $user = Auth::user();
        foreach ($data as $votes) {
            foreach ($votes as $vote) {
                foreach ($vote['answers_ids'] as $answer_id) {
                    PollVote::query()->create([
                        'article_id' => $article->id,
                        'question_id' => $vote['question_id'],
                        'answer_id' => $answer_id,
                        'user_id' => $user->id
                    ]);
                }
            }
        }

        return response()->json(['message' => "Success"]);
    }

    /**
     * @return JsonResponse
     */
    public function makeViewedArticles(): JsonResponse
    {
        $user = Auth::user();

        $unviewedArticles = Article::getUnviewedArticle($user->id);

        foreach ($unviewedArticles as $item) {
            $item->views()->attach($user->id);
        }

        return response()->json(['message' => "Success"]);
    }


}
