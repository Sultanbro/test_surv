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
        $ownerId = auth()->id();

        $portal = Portal::where('tenant_id', $tenantId)->firstorfail();

        if ($portal->owner_id != $ownerId)
        {
            throw new Exception("Нет доступа.");
        }

        return $next($request);
    }
}
