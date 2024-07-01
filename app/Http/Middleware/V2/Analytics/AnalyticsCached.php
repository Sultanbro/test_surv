<?php

namespace App\Http\Middleware\V2\Analytics;

use App\CacheStorage\AnalyticCacheStorage;
use App\Enums\V2\Analytics\AnalyticEnum;
use App\Helpers\DateHelper;
use App\Models\Analytics\AnalyticColumn;
use App\Models\Analytics\AnalyticRow;
use App\Models\Analytics\AnalyticStat;
use App\Traits\AnalyticTrait;
use Closure;
use Illuminate\Http\Request;

class AnalyticsCached
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
         * Записываем в кэш колонки.
         */
        $keyColumn = AnalyticCacheStorage::key($date, AnalyticEnum::ANALYTIC_COLUMN, $dto->groupId);

        if (!AnalyticCacheStorage::has($keyColumn))
        {
            $columns = AnalyticColumn::query()
                ->where('date', $date)
                ->where('group_id', $dto->groupId)
                ->get();

            AnalyticCacheStorage::put($keyColumn, $columns);
        }

        /**
         * Записываем в кэш строки.
         */
        $keyRow = AnalyticCacheStorage::key($date, AnalyticEnum::ANALYTIC_ROW, $dto->groupId);

        if (!AnalyticCacheStorage::has($keyRow))
        {
            $rows = AnalyticRow::query()
                ->where('date', $date)
                ->where('group_id', $dto->groupId)
                ->get();

            AnalyticCacheStorage::put($keyRow, $rows);
        }

        /**
         * Записываем в кэш статистику.
         */
        $keyStat = AnalyticCacheStorage::key($date, AnalyticEnum::ANALYTIC_STAT, $dto->groupId);

        if (!AnalyticCacheStorage::has($keyStat))
        {
            $stats = AnalyticStat::query()
                ->where('date', $date)
                ->where('group_id', $dto->groupId)
                ->get();

            AnalyticCacheStorage::put($keyStat, $stats);
        }

        return $next($request);
    }
}
