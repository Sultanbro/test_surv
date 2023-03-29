<?php

namespace App\Http\Middleware\Portal;

use App\Models\Portal\Portal;
use Closure;
use Exception;
use Illuminate\Support\Facades\Auth;

class IsOwner {

    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     * @throws Exception
     */
    public function handle($request, Closure $next) {

        $tenantId = tenant('id');
        $jobtronUserId = \DB::connection('mysql')
            ->table('users')
            ->where('email', auth()->user()->email)
            ->pluck('id')
            ->first();

        $portal = Portal::where('tenant_id', $tenantId)
            ->firstorfail();

        if (!$jobtronUserId || $portal->owner_id != $jobtronUserId)
        {
            throw new Exception("Нет доступа.");
        }

        return $next($request);
    }
}
