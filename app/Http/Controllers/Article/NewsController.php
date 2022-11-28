<?php

namespace App\Http\Controllers\Article;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\UserRequest;
use App\Http\Resources\Responses\JsonSuccessResponse;
use App\Http\Resources\Users\UserResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;

class NewsController extends Controller
{
    public function index()
    {
       View::share('title', 'NEWS');
        View::share('menu', 'news');

        return view('news')->with([
            'page' => 'news'
        ]);
    }
    public function user(UserRequest $request): JsonResponse
    {
        return response()->json(
            new JsonSuccessResponse(
                __('model/me.show'),
                (new UserResource(Auth::user()))->toArray($request)
            )
        );
    }
}
