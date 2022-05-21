<?php

namespace App\Http\Middleware;

use Closure;

class TestMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        \Debugbar::disable();
        if (auth()->user() && in_array(auth()->id(), [5]) && env('DEBUGBAR_ENABLED', false)) {
           // \Debugbar::enable();
        }
        else {
            \Debugbar::disable();
           
        }

        return $next($request);
    }
}