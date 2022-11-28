<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Position;
use App\User;
use Spatie\Permission\Models\Role;
use DB;

class CheckPermissions
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {   
        if(request()->getHost() == config('app.domain')) {
            return $next($request);
        }

        if(!auth()->user()) {
            return $next($request);
        }

        $roles = [];
        $pos = Position::find(auth()->user()->position_id);
        if($pos) $roles = array_merge($roles, DB::table('model_has_roles')
                    ->where('model_type', 'App\\Position')
                    ->where('model_id', $pos->id)
                    ->get()->pluck('role_id')->toArray());

        $groups = auth()->user()->inGroups();
        if(count($groups) > 0) {
            $roles = array_merge($roles, DB::table('model_has_roles')
                ->where('model_type', 'App\\ProfileGroup')
                ->whereIn('model_id', collect($groups)->pluck('id'))
                ->get()->pluck('role_id')->toArray());
        }

        $has_roles = array_merge($roles, DB::table('model_has_roles')
                    ->where('model_type', 'App\\User')
                    ->where('model_id', auth()->id())
                    ->get()->pluck('role_id')->toArray());
        
       
        $new_roles = array_diff($roles, $has_roles);
        $new_roles = array_values($new_roles);

        $roles = Role::whereIn('id', array_unique($has_roles))->get(['name'])->pluck('name')->toArray();

        // $user = User::find(auth()->id());
        //dd($roles);
        // $user->syncRoles($roles);
        
        
        // foreach ($roles as $key => $role) {
        //     auth()->user()->assignRole($role);
        // }
            
        auth()->user()->assignRole($roles);
        auth()->user()->assignRole($new_roles); 

        // dd($roles);
        //    dd(auth()->user()->can('quality_view'));
        return $next($request);
    }
}
