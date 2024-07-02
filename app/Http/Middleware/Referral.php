<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class Referral
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next): mixed
    {
        if (!$request->hasCookie('referral') && $request->query('ref')) {
            return redirect($request->url())
                ->withCookie(cookie()->forever('referral', $request->query('ref')))
                ->withCookie(cookie()->forever('source', $request->query('source')));
        }

        return $next($request);
    }
}
