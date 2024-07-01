<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class OnlyCentralDomain
{
    public function handle(Request $request, Closure $next)
    {
        $centralHost = request()->getHost() === config('app.domain');

        if (!$centralHost) return redirect()->back();

        return $next($request);
    }
}
