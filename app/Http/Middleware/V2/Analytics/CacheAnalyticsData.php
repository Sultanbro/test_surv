<?php

namespace App\Http\Middleware\V2\Analytics;

use App\CacheStorage\AnalyticCacheStorage;
use App\Enums\V2\Analytics\AnalyticEnum;
use App\Helpers\DateHelper;
use App\Models\Analytics\TopValue;
use App\ProfileGroup;
use App\Traits\AnalyticTrait;
use Closure;
use Illuminate\Http\Request;

class CacheAnalyticsData
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

        if (!AnalyticCacheStorage::has(AnalyticEnum::GROUP_KEY))
        {
            $groups = ProfileGroup::query()->get()->toArray();
            AnalyticCacheStorage::put(AnalyticEnum::GROUP_KEY, $groups);
        }


        return $next($request);
    }
}
