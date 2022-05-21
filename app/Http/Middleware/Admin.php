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

        if(!Auth::user()) return $next($request);
        if(!User::isUserAdmin(Auth::user()->ID)) {

            Auth::logout();
            return redirect('/');
        }

        $admin = AdminModel::where('owner_id', 18)->first();

        if(Auth::user() && (in_array(Auth::user()->ID,$admin->users) || Auth::user()->ID == $admin->owner_id)) {
            Auth::user()->is_admin = true;
        } else {
            Auth::user()->is_admin = false;
        }

        return $next($request);
    }
}
