<?php

namespace App\Http\Middleware\V2\Analytics;

use App\CacheStorage\AnalyticCacheStorage;
use App\Enums\V2\Analytics\AnalyticEnum;
use App\Helpers\DateHelper;
use App\Models\Analytics\Activity;
use App\ProfileGroup;
use App\Traits\AnalyticTrait;
use Closure;
use Illuminate\Http\Request;

class GroupsAndActivitiesCached
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

        /**
         * Записываем все группы.
         */
        if (!AnalyticCacheStorage::has(AnalyticEnum::GROUP_KEY))
        {
            AnalyticCacheStorage::put(AnalyticEnum::GROUP_KEY, ProfileGroup::all());
        }

        /**
         * Записываем в кэш все активности.
         */
        if (!AnalyticCacheStorage::has(AnalyticEnum::ANALYTIC_ACTIVITIES))
        {
            AnalyticCacheStorage::put(AnalyticEnum::ANALYTIC_ACTIVITIES, Activity::all());
        }

        return $next($request);
    }
}
