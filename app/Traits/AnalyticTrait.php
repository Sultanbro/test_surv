<?php

namespace App\Traits;

use App\CacheStorage\AnalyticCacheStorage;
use App\DTO\Analytics\V2\GetAnalyticDto;
use App\Enums\V2\Analytics\AnalyticEnum;
use Carbon\Carbon;
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
     * @param int $groupId
     * @param string $date
     * @return mixed
     */
    public function employees(
        int $groupId,
        string $date
    )
    {
        $group      = $this->groups()->where('id', $groupId)->first();
        $dateFrom   = Carbon::createFromDate($date)->endOfMonth()->format('Y-m-d');
        $dateTo     = Carbon::createFromDate($date)->addMonth()->startOfMonth()->format('Y-m-d');

        return $group->actualAndFiredEmployees($dateFrom, $dateTo);
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
     * @param ?int $groupId
     * @return Collection
     */
    public function columns(
        string $date,
        int $groupId = null
    ): Collection
    {
        return collect(AnalyticCacheStorage::get(AnalyticCacheStorage::key($date, AnalyticEnum::ANALYTIC_COLUMN, $groupId)));
    }

    /**
     * AnalyticRow.
     *
     * Строки.
     *
     * @param string $date
     * @param int|null $groupId
     * @return Collection
     */
    public function rows(
        string $date,
        int $groupId = null
    ): Collection
    {
        return collect(AnalyticCacheStorage::get(AnalyticCacheStorage::key($date, AnalyticEnum::ANALYTIC_ROW, $groupId)));
    }

    /**
     * AnalyticStat.
     *
     * Статистика.
     *
     * @param string $date
     * @param int|null $groupId
     * @return Collection
     */
    public function statistics(
        string $date,
        int $groupId = null
    ): Collection
    {
        return collect(AnalyticCacheStorage::get(AnalyticCacheStorage::key($date, AnalyticEnum::ANALYTIC_STAT, $groupId)));
    }

    /**
     * @param string $date
     * @param int|null $groupId
     * @return Collection
     */
    public function decompositions(
        string $date,
        int $groupId = null
    ): Collection
    {
        return collect(AnalyticCacheStorage::get(AnalyticCacheStorage::key($date, AnalyticEnum::ANALYTIC_DECOMPOSITIONS, $groupId)));
    }
}