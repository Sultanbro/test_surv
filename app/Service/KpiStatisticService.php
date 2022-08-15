<?php

namespace App\Service;

use App\Models\Kpi\KpiItem;
use App\User;
use Illuminate\Support\Facades\DB;

class KpiStatisticService
{
    public function get(User $user)
    {
        return $this->calculateStatistics($user);
    }

    /**
     * @param User $user
     * @return array
     */
    private function getUserStatistics(User $user): array
    {
        return $user->statistics()
            ->leftJoin('activities', 'activities.id', '=', 'user_stats.activity_id')
            ->join('kpi_items', 'kpi_items.activity_id', '=', 'activities.id')
            ->get(['kpi_items.activity_id', 'kpi_items.kpi_id', 'value', 'kpi_items.plan', 'kpi_items.name'])->toArray();
    }

    /**
     * @param User $user
     * @return array
     */
    private function calculateStatistics(User $user): array
    {
        $statistics = $this->getUserStatistics($user);

        foreach ($statistics as $key => $statistic)
        {
            $statistics[$key]['percent'] = ((int) $statistic['value'] / (int) $statistic['plan']) * 100;
        }

        return $statistics;
    }
}