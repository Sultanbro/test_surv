<?php

namespace App\Repositories\Analytics;

use App\Models\Analytics\AnalyticColumn;
use App\Models\Analytics\AnalyticRow;
use App\Repositories\CoreRepository;
use Illuminate\Database\Eloquent\Collection;

class AnalyticRowRepository extends CoreRepository
{
    /**
     * @return Collection<AnalyticRow>
     */
    public function getByGroupId(int $groupId, string $date): Collection
    {
        return AnalyticRow::query()
            ->where('date', $date)
            ->where('group_id', $groupId)
            ->orderByDesc('order')
            ->get();
    }

    protected function getModelClass(): string
    {
        return AnalyticColumn::class;
    }
}