<?php

namespace App\Http\Middleware;

use Closure;
use Auth;

class CheckSuperUser
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
        if(!in_array(Auth::user()->id, [5, 18])) return redirect('/videolearning');
        return $next($request); 
    }

}
