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
            'top_view' => $user->is_admin == 1 || in_array($user->id, []),
            'hr_view' => $user->is_admin == 1 || in_array($user->id, [4444,5263,9974,10230,3865]),
            'analytics_view' => $user->is_admin == 1 || in_array($user->id, [13410,4444,5,11533,5494,12787,13583,3782,4593,3794,3423,3865,3957,1739,2966,13337,3423,13410,3865,4143,6288,5673,14548]),
            'tabel_view' => $user->is_admin == 1 || in_array($user->id, [13410,4444,5263,9974,11533,5494,12787,1358, 3460, 1739,2966,13337,3794,3423,4143,13583,13410,3865,10230,6288,5673,14548]),
            'entertime_view' => $user->is_admin == 1 || in_array($user->id, [13410,5263,9974,11533,5494,12787,13583, 1739,2966,3794,3423,4143,3865,6288]),
            'salaries_view' => $user->is_admin == 1 || in_array($user->id, [13410,4444,5263,9974,11533,5494,12787,13583,1739,2966,3794,3423,4143,3865,6288]),
            'quality_view' => $user->is_admin == 1 || in_array($user->id, [13410,11533,5494,12787,13583,3460,3423,3794,3965,4143,5494,8257,9974,11208,3423,3865,10230,6288,5673,5263,3957]),
            'users_view' => $user->is_admin == 1 || in_array($user->id, [5263,9974]), 
            'settings_view' => $user->is_admin == 1 || in_array($user->id, []),
            'cabinet_view' => $user->is_admin == 1,
            'courses_view' => $user->is_admin == 1 || in_array($user->id, []),
            
        ];

        
        // $admin = AdminModel::where('owner_id', 18)->first();

        // if(Auth::user()->ID == 18 || ($admin && $admin->users && in_array(Auth::user()->ID, $admin->users))) {
        //     Auth::user()->is_admin = true;
        // } else {
        //     Auth::user()->is_admin = false;
        // }

        $groups = [];
        
        $_groups = \App\ProfileGroup::where('active', 1)->get();
        foreach ($_groups as $key => $group) {
            if($group->editors_id == null) continue;
            if(in_array(auth()->id(), json_decode($group->editors_id))) {
                array_push($groups, $group->id);
            }
        }

        Auth::user()->groups = $groups;
        Auth::user()->is_admin = Auth::user()->is_admin == 1;

        return $next($request);
    }
}
