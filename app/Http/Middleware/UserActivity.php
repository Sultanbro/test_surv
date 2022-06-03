<?php

namespace App\Http\Middleware;

use Closure;
use Auth;

class UserActivity
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
    
        // if (Auth::check()) {
        //     $user = Auth::user();
        //     $user->LAST_LOGIN = date('Y-m-d H:i:s');
        //     $user->reminder_sent = 0;
        //     $user->audio_reminder_sent = 0;
        //     $user->save();

        //     // if($user->id != 5) { // Али Акпанов Показать debugbar
        //     //     \Debugbar::disable();
        //     // }
        // } else {
        //     \Debugbar::disable();
        // }

        
        return $next($request);
    }
}
