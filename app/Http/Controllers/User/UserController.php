<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Resources\Responses\JsonSuccessResponse;
use App\Http\Resources\Users\UserResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use \Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * @return JsonResponse
     */
    public function me(Request $request): JsonResponse
    {
        return response()->json(
            new JsonSuccessResponse(
                __('model/me.show'),
                (new UserResource(Auth::user()))->toArray($request)
            )
        );
    }
}
