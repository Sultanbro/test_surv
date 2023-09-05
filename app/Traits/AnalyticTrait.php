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
     * AnalyticColumn.
     *
     * Колонки.
     *
     * @return Collection
     */
    public function columns(): Collection
    {
        return collect(AnalyticCacheStorage::get(AnalyticEnum::ANALYTIC_COLUMN));
    }

    /**
     * AnalyticRow.
     *
     * Строки.
     *
     * @return Collection
     */
    public function rows(): Collection
    {
        return collect(AnalyticCacheStorage::get(AnalyticEnum::ANALYTIC_COLUMN));
    }

    /**
     * AnalyticStat.
     *
     * Статистика.
     *
     * @return Collection
     */
    public function statistics(): Collection
    {
        return collect(AnalyticCacheStorage::get(AnalyticEnum::ANALYTIC_COLUMN));
    }
}