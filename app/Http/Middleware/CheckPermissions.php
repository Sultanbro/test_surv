<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Position;
use App\User;
use Spatie\Permission\Models\Role;

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
        if(!auth()->user())return $next($request);

        $roles = [];
        $pos = Position::with('roles')->find(auth()->user()->position_id);
        if($pos) $roles = array_merge($roles, $pos->roles->pluck('id')->toArray());

        $groups = auth()->user()->inGroups();
        if(count($groups) > 0) {
            foreach ($groups as $key => $group) {
                $roles = array_merge($roles, $group->roles->pluck('id')->toArray());
            }
        }

        $roles = array_merge($roles, auth()->user()->roles()->pluck('id')->toArray());
        

        $roles = Role::whereIn('id', array_unique($roles))->get(['name'])->pluck('name')->toArray();

        // $user = User::find(auth()->id());
        //dd($roles);
        // $user->syncRoles($roles);
        auth()->user()->syncRoles($roles);
        

        return $next($request);
    }
}
