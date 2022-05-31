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
  

        $user = Auth::user();
        Auth::user()->can = [
            'top_view' => $user->is_admin == 1 || in_array($user->id, [5,18,84,4444,13865]),
            'hr_view' => $user->is_admin == 1 || in_array($user->id, [5,18,84,4444,13865]),
            'analytics_view' => $user->is_admin == 1 || in_array($user->id, [5,18,84,4444,13865]),
            'tabel_view' => $user->is_admin == 1 || in_array($user->id, [5,18,84,4444,13865]),
            'entertime_view' => $user->is_admin == 1 || in_array($user->id, [5,18,84,4444,13865]),
            'salaries_view' => $user->is_admin == 1 || in_array($user->id, [5,18,84,4444,13865]),
            'quality_view' => $user->is_admin == 1 || in_array($user->id, [5,18,84,4444,13865]),
            'users_view' => $user->is_admin == 1 || in_array($user->id, [5,18,84,4444,13865]),
            'settings_view' => $user->is_admin == 1 || in_array($user->id, [5,18,84,4444,13865]),
            'cabinet_view' => $user->is_admin == 1,
            'courses_view' => $user->is_admin == 1 || in_array($user->id, [5,18,84,4444,13865]),
            
        ];

        // $admin = AdminModel::where('owner_id', 18)->first();

        // if(Auth::user()->ID == 18 || ($admin && $admin->users && in_array(Auth::user()->ID, $admin->users))) {
        //     Auth::user()->is_admin = true;
        // } else {
        //     Auth::user()->is_admin = false;
        // }

        Auth::user()->is_admin = Auth::user()->is_admin == 1;

        return $next($request);
    }
}
