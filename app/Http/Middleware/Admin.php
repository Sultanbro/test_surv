<?php

namespace App\Http\Middleware;

use App\User;
use App\Admin as AdminModel;
use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;

class Admin {

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next) {
  
        if($request->getPathInfo()=='/logout') return $next($request);
        if(!Auth::user()) return $next($request);
  

        $admin = AdminModel::where('owner_id', 18)->first();



        Auth::user()->is_admin = Auth::user()->is_admin == 1;

        return $next($request);
    }
}
