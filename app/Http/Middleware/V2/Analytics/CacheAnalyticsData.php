<?php

namespace App\Http\Middleware\V2\Analytics;

use App\CacheStorage\AnalyticCacheStorage;
use App\Enums\V2\Analytics\AnalyticEnum;
use App\Helpers\DateHelper;
use App\Models\Analytics\Activity;
use App\Models\Analytics\AnalyticColumn;
use App\Models\Analytics\AnalyticRow;
use App\Models\Analytics\AnalyticStat;
use App\Models\Analytics\DecompositionValue;
use App\Models\Analytics\TopValue;
use App\ProfileGroup;
use App\Support\Core\CustomException;
use App\Traits\AnalyticTrait;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

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
        $dto    = $this->requestToDto($request);
        $date   = DateHelper::firstOfMonth($dto->year, $dto->month);

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

        /**
         * Записываем в кэш колонки.
         */
        if (!AnalyticCacheStorage::has(AnalyticEnum::ANALYTIC_COLUMN))
        {
            $columns = AnalyticColumn::query()->where('date', $date)->get();
            AnalyticCacheStorage::put(AnalyticCacheStorage::key($date, AnalyticEnum::ANALYTIC_COLUMN), $columns);
        }

        /**
         * Записываем в кэш строки.
         */
        if (!AnalyticCacheStorage::has(AnalyticEnum::ANALYTIC_ROW))
        {
            $rows = AnalyticRow::query()->where('date', $date)->get();
            AnalyticCacheStorage::put(AnalyticCacheStorage::key($date, AnalyticEnum::ANALYTIC_ROW), $rows);
        }

        /**
         * Записываем в кэш статистику.
         */
        if (!AnalyticCacheStorage::has(AnalyticEnum::ANALYTIC_STAT))
        {
            $stats = AnalyticStat::query()->where('date', $date)->get();
            AnalyticCacheStorage::put(AnalyticCacheStorage::key($date, AnalyticEnum::ANALYTIC_STAT), $stats);
        }

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
