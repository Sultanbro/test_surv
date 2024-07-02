<?php

namespace App\Http\Middleware;

use Closure;

class IsAdminSubDomain
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // admin.jobtron.org
        if(request()->getHost() !== 'admin.' .config('app.domain')) {
            return redirect('/');
        }

        return $next($request);
    }
}