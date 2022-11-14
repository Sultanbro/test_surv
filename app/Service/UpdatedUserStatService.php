<?php

namespace App\Service;

use App\Helpers\CalculateCarried;
use App\Repositories\KpiItemRepository;
use App\Repositories\UpdatedUserStatRepository;
use App\User;
use Carbon\Carbon;

class UpdatedUserStatService
{
    public UpdatedUserStatRepository $repository;

    public function __construct()
    {
        $this->repository = new UpdatedUserStatRepository();
    }

    public function calculateStat(User $user, Carbon $date): float|int
    {
        $statistics = $this->repository->retrieveLastRecordUpdatedStatisticsForEachKpi($user, $date)->get() ?? [];

        $amount = 0;
        foreach ($statistics as $statistic) {
            if ($statistic != null) {
                $kpiItem  = (new KpiItemRepository)->joinKpiItemsWithKpi($statistic->kpi_item_id, $date);
                if ($kpiItem != null) {
                    $amount  += CalculateCarried::calculate($kpiItem, $statistic, $user);
                }
            }
        }

        return $amount;

    }

    /**
     * Создаем если нет таких данных а если есть то просто обновляем.
     *
     * @param int $userId
     * @param int $activityId
     * @param int $kpiItemId
     * @param string $date
     * @param string $value
     * @return mixed
     */
    public function updateOrCreate(
        int $userId,
        int $activityId,
        int $kpiItemId,
        string $date,
        string $value
    )
    {
        return $this->repository->updateOrCreateUpdatedUserStat($userId, $activityId, $kpiItemId, $date, $value);
    }
}