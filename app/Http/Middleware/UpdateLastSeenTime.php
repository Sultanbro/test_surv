<?php

namespace App\Http\Middleware;

use App\User;
use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class UpdateLastSeenTime
{
    public function handle(Request $request, Closure $next)
    {
        $authId = auth()->id();
        if (Auth::check()) {
            $user = User::getUserById($authId);
            $user->last_seen = now();
            $user->save();
        }

        return $next($request);
    }
}
