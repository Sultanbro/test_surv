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
        if($request->getPathInfo() =='/logout') return $next($request);
  
        $groups = [];
        
        $_groups = \App\ProfileGroup::where('active', 1)->get();
        foreach ($_groups as $key => $group) {
            if($group->editors_id == null) continue;
            if(in_array(auth()->id(), json_decode($group->editors_id))) {
                array_push($groups, $group->id);
            }
        }


        Auth::user()->groups = $groups;

        if(Auth::user()->is_admin == 1) {
            Auth::user()->groups = $_groups->pluck('id')->toArray();
        }

       // if(auth()->id() == 5) dd(auth()->user()->can('salaries_view'));
      //  Auth::user()->is_admin = Auth::user()->is_admin == 1;

      
        
        return $next($request);
    }
}
