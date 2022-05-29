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
        return $next($request);
        if($request->getPathInfo()=='/logout') return $next($request);
        if(!Auth::user()) return $next($request);
        if(!User::isUserAdmin(Auth::user()->id)) {

            Auth::logout();
            return redirect('/');
        }

        $admin = AdminModel::where('owner_id', 18)->first();



        if((in_array(Auth::user()->id,$admin->users) || Auth::user()->id == $admin->owner_id)) {
            Auth::user()->is_admin = true;
        } else {
            Auth::user()->is_admin = false;
        }

        return $next($request);
    }
}
