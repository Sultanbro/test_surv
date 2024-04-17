<?php

namespace App\Repositories;

use App\Models\Analytics\UpdatedUserStat as Model;
use App\Repositories\Interfaces\UpdatedUserStatRepositoryInterface;
use App\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

class UpdatedUserStatRepository extends CoreRepository implements UpdatedUserStatRepositoryInterface
{
    public function retrieveLastRecordUpdatedStatisticsForEachKpi(User $user, Carbon $date): Builder
    {
        return $this->model()
            ->query()
            ->where('user_id', $user->id)
            ->whereYear('date', $date->year)
            ->whereMonth('date', $date->month)
            ->whereRaw('id IN (SELECT MAX(id) FROM updated_user_stats GROUP BY kpi_item_id)')
            ->groupBy(['user_id', 'kpi_item_id', 'activity_id']);
    }

    public function updateOrCreateUpdatedUserStat(
        int    $userId,
        int    $activityId,
        int    $kpiItemId,
        string $date,
        string $value
    )
    {
        return $this->model()->updateOrCreate(
            [
                'user_id' => $userId,
                'date' => $date,
                'activity_id' => $activityId,
                'kpi_item_id' => $kpiItemId
            ],
            [
                'user_id' => $userId,
                'date' => $date,
                'activity_id' => $activityId,
                'kpi_item_id' => $kpiItemId,
                'value' => $value
            ]
        );
    }

    protected function getModelClass(): string
    {
        return Model::class;
    }
}