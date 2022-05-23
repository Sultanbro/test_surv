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
<<<<<<< HEAD


=======
        \Debugbar::disable();
>>>>>>> ca8840394c6d7bce637bee9f22b98bddf01f0acd
        if (auth()->user() && in_array(auth()->id(), [5]) && env('DEBUGBAR_ENABLED', false)) {
           // \Debugbar::enable();
        }
        else {
            \Debugbar::disable();

        }




        return $next($request);
    }
}