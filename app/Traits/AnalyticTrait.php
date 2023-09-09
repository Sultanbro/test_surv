<?php

namespace App\Traits;

use App\CacheStorage\AnalyticCacheStorage;
use App\DTO\Analytics\V2\GetAnalyticDto;
use App\Enums\V2\Analytics\AnalyticEnum;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

trait AnalyticTrait
{
    /**
     * @param Request $request
     * @return GetAnalyticDto
     */
    public function requestToDto(
        Request $request
    ): GetAnalyticDto
    {
        $data = $request->all();

        return GetAnalyticDto::fromArray($data);
    }

    /**
     * ProfileGroup.
     *
     * Группы.
     *
     * @return Collection
     */
    public function groups(): Collection
    {
        return collect(AnalyticCacheStorage::get(AnalyticEnum::GROUP_KEY));
    }

    /**
     * ProfileGroup.
     *
     * Группы.
     *
     * @return Collection
     */
    public function activities(): Collection
    {
        return collect(AnalyticCacheStorage::get(AnalyticEnum::ANALYTIC_ACTIVITIES));
    }

    /**
     * AnalyticColumn.
     *
     * Колонки.
     *
     * @param string $date
     * @return Collection
     */
    public function columns(
        string $date
    ): Collection
    {
        return collect(AnalyticCacheStorage::get(AnalyticCacheStorage::key($date, AnalyticEnum::ANALYTIC_COLUMN)));
    }

    /**
     * AnalyticRow.
     *
     * Строки.
     *
     * @param string $date
     * @return Collection
     */
    public function rows(
        string $date
    ): Collection
    {
        return collect(AnalyticCacheStorage::get(AnalyticCacheStorage::key($date, AnalyticEnum::ANALYTIC_ROW)));
    }

    /**
     * AnalyticStat.
     *
     * Статистика.
     *
     * @param string $date
     * @return Collection
     */
    public function statistics(
        string $date
    ): Collection
    {
        return collect(AnalyticCacheStorage::get(AnalyticCacheStorage::key($date, AnalyticEnum::ANALYTIC_STAT)));
    }

    /**
     * @param string $date
     * @return Collection
     */
    public function decompositions(
        string $date
    ): Collection
    {
        return collect(AnalyticCacheStorage::get(AnalyticCacheStorage::key($date, AnalyticEnum::ANALYTIC_DECOMPOSITIONS)));
    }
}