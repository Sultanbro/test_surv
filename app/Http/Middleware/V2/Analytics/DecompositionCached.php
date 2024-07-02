<?php

namespace App\Http\Middleware\V2\Analytics;

use App\CacheStorage\AnalyticCacheStorage;
use App\Enums\V2\Analytics\AnalyticEnum;
use App\Helpers\DateHelper;
use App\Models\Analytics\DecompositionValue;
use App\Traits\AnalyticTrait;
use Closure;
use Illuminate\Http\Request;

class DecompositionCached
{
    use AnalyticTrait;

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $dto    = $this->requestToDto($request);
        $date   = DateHelper::firstOfMonth($dto->year, $dto->month);

        /**
         * Записываем в кэш декомпозицию.
         */
        if (!AnalyticCacheStorage::has(AnalyticEnum::ANALYTIC_DECOMPOSITIONS))
        {
            $decompositions = DecompositionValue::query()->where('date', $date)->get();
            AnalyticCacheStorage::put(AnalyticCacheStorage::key($date, AnalyticEnum::ANALYTIC_DECOMPOSITIONS), $decompositions);
        }

        return $next($request);
    }
}
