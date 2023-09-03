<?php

namespace App\Http\Middleware\V2\Analytics;

use App\DTO\Analytics\V2\GetAnalyticDto;
use App\Http\Requests\V2\Analytics\GetAnalyticsRequest;
use App\ProfileGroup;
use App\Support\Core\CustomException;
use App\User;
use Closure;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class CheckAnalyticPermission
{
    /**
     * Handle an incoming request.
     *
     * @param Request<GetAnalyticsRequest> $request
     * @param \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|RedirectResponse) $next
     * @return \Illuminate\Http\Response|RedirectResponse
     * @throws Throwable
     */
    public function handle(Request $request, Closure $next)
    {
        $user   = auth()->user() ?? User::findOrFail(5);
        $dto    = $this->requestToDto($request);
        $group  = ProfileGroup::findOrFail($dto->groupId);

        if (!$user->isAdmin()) {
            $editors = json_decode($group->editors_id) ?? [];

            new CustomException(!in_array($user->id, $editors), Response::HTTP_FORBIDDEN, [
                'message' => 'У вас нет прав доступа для просмотра Аналитики. Обратитесь к администратору.'
            ]);
        }

        return $next($request);
    }

    /**
     * @param Request $request
     * @return GetAnalyticDto
     */
    public function requestToDto(
        Request $request
    ): GetAnalyticDto
    {
        $data = $request->all();

        return GetAnalyticDto::fromArray($data);
    }
}
