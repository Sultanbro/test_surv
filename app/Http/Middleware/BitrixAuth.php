<?php

namespace App\Http\Middleware;

use App\User;
use Closure;
use Illuminate\Support\Facades\Cookie;

class BitrixAuth {

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next) {
        // для локального запуска и тестирования в .env добавил параметр BX_USER_ID=1
        if(env('USER_ID')){
            return $next($request);
        }
        session_start();

        if(!isset($_SESSION["SESS_AUTH"]["AUTHORIZED"]) || $_SESSION["SESS_AUTH"]["AUTHORIZED"]!='Y') {
            return redirect('/auth');
        }

        if (!isset($_COOKIE['PHPSESSID']) || session_id() != $_COOKIE['PHPSESSID']) {
            return redirect('/auth');
        }

        if(!$_SESSION["SESS_AUTH"]["USER_ID"]) {
            return redirect('/auth');
        }

        $user_id = $_SESSION["SESS_AUTH"]["USER_ID"];

        $user = User::where('id', $user_id)->first();
        if(!$user) {
            return redirect('/auth');
        }

        return $next($request);
    }
}
