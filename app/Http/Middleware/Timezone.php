<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Timezone {

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next) {
        
        $offset = Auth::user()->timezone;

        date_default_timezone_set('Etc/GMT'.sprintf('%+d', $offset*-1));

        //config('app.timezone', $timezone_name);
        //config('database.mysql.timezone',$user->timezone); //с конфигами не работает


        DB::statement("SET time_zone='".sprintf('%+d:00', $offset)."'");

        return $next($request);
    }
}
