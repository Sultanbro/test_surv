<?php

namespace App\Http\Middleware;

use Closure;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckIsAdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse) $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     * @throws Exception
     */
    public function handle(Request $request, Closure $next)
    {
        $user = auth()->user();

        if (!$user->is_admin && (!$user->can('awards_edit') || !$user->can('awards_view'))){
            throw new \Exception('У вас нет права Администратора!');
        }
        return $next($request);
    }
}
