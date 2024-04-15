<?php

namespace App\Http\Middleware;

use App\Exceptions\Tariff\TariffExpiredException;
use App\Models\Tenant;
use App\User;
use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class TariffMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure(Request): (Response|RedirectResponse) $next
     * @return Response|RedirectResponse
     * @throws TariffExpiredException
     */
    public function handle(Request $request, Closure $next): Response|RedirectResponse
    {
        /** @var Tenant $tenant */
        $tenant = tenant();
        /** @var User $user */
        $user = $request->user();

        if ($user->isOwner() || $tenant->hasActiveTariff()) return $next($request);

        throw new TariffExpiredException("Тарифный план истек! Обратитесь к руководителю.", 403);
    }
}
