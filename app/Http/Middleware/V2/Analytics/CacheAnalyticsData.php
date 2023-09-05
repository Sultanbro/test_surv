<?php

namespace App\Http\Middleware\V2\Analytics;

use App\CacheStorage\AnalyticCacheStorage;
use App\Enums\V2\Analytics\AnalyticEnum;
use App\Helpers\DateHelper;
use App\Models\Analytics\Activity;
use App\Models\Analytics\AnalyticColumn;
use App\Models\Analytics\AnalyticRow;
use App\Models\Analytics\AnalyticStat;
use App\Models\Analytics\TopValue;
use App\ProfileGroup;
use App\Traits\AnalyticTrait;
use Closure;
use Illuminate\Http\Request;

class CacheAnalyticsData
{
    use AnalyticTrait;
    /**
     * При обращений в аналитику надо записать все данные в кэш.
     *
     * Потому что аналитика скорость очень медленная.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $date = now()->firstOfMonth()->format('Y-m-d');

        /**
         * Записываем все группы.
         */
        if (!AnalyticCacheStorage::has(AnalyticEnum::GROUP_KEY))
        {
            $groups = ProfileGroup::query()->get()->toArray();
            AnalyticCacheStorage::put(AnalyticEnum::GROUP_KEY, $groups);
        }

        /**
         * Записываем в кэш все активности.
         */
        if (!AnalyticCacheStorage::has(AnalyticEnum::ANALYTIC_ACTIVITIES))
        {
            $activities = Activity::all()->toArray();
            AnalyticCacheStorage::put(AnalyticEnum::ANALYTIC_ACTIVITIES, $activities);
        }

        /**
         * Записываем в кэш колонки.
         */
        if (!AnalyticCacheStorage::has(AnalyticEnum::ANALYTIC_COLUMN))
        {
            $columns = AnalyticColumn::query()->where('date', $date)->get()->toArray();
            AnalyticCacheStorage::put(AnalyticEnum::ANALYTIC_COLUMN, $columns);
        }

        /**
         * Записываем в кэш строки.
         */
        if (!AnalyticCacheStorage::has(AnalyticEnum::ANALYTIC_ROW))
        {
            $rows = AnalyticRow::query()->where('date', $date)->get()->toArray();
            AnalyticCacheStorage::put(AnalyticEnum::ANALYTIC_ROW, $rows);
        }

        /**
         * Записываем в кэш статистику.
         */
        if (!AnalyticCacheStorage::has(AnalyticEnum::ANALYTIC_STAT))
        {
            $stats = AnalyticStat::query()->where('date', $date)->get()->toArray();
            AnalyticCacheStorage::put(AnalyticEnum::ANALYTIC_STAT, $stats);
        }

        return $next($request);
    }
}
