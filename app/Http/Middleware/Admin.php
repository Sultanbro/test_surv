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

        if(request()->getHost() == config('app.domain')) {
            return $next($request);
        }

        if(!Auth::user()) {
            return $next($request);
        }
        
        if($request->getPathInfo() =='/logout') {
            return $next($request);
        }
  
        $groups = [];
        
        $_groups = \App\ProfileGroup::where('active', 1)->get();

        foreach ($_groups as $group) {

            if($group->editors_id == null and $group->editors_id == 'null') {
                continue;
            }

            if(in_array( auth()->id(), json_decode($group->editors_id) )) {
                array_push($groups, $group->id);
            }
        }
        

        Auth::user()->groups = $groups;

        if(Auth::user()->is_admin == 1 || auth()->user()->groups_all == 1) {
            Auth::user()->groups = \App\ProfileGroup::get(['id'])->pluck('id')->toArray();
        } 
        
        return $next($request);
    }
}
