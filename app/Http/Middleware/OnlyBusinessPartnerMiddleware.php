<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class OnlyBusinessPartnerMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure(Request): (Response|RedirectResponse) $next
     * @return Response|RedirectResponse|string
     */
    public function handle(Request $request, Closure $next): Response|RedirectResponse|string
    {
        if (tenant('id') !== config('tenancy.default_tenant')) {
            return response()->json(
                [
                    'message' => 'Пошёл к чёрту отсюда',
                ]
                , status: 403
            );
        }
        return $next($request);
    }
}
