<?php

namespace App\Http\Controllers\Article\Dictionary;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dictionary\DictionaryRequest;
use App\Http\Resources\Dictionary\DictionaryResource;
use App\Http\Resources\Responses\JsonSuccessResponse;
use Illuminate\Http\JsonResponse;

class DictionaryController extends Controller
{
    public function index(DictionaryRequest $request): JsonResponse
    {
        return response()->json(
            new JsonSuccessResponse(
                __('model/dictionary.index'),
                (new DictionaryResource(null))->toArray($request)
            )
        );
    }
}
