<?php
declare(strict_types=1);

namespace App\Models\Kpi\Builder;

use App\Models\Kpi\Kpi;
use Illuminate\Database\Eloquent\Builder;

final class KpiSearch extends Builder
{
    /**
     * @param string $searchWord
     * @return Builder
     */
    public static function search(
        string $searchWord
    ): Builder
    {
        return Kpi::query()
            ->select(
                'kpis.*'
            )
            ->leftJoin('users as u', function ($join) {
                $join->on('u.id', '=', 'kpis.targetable_id')
                    ->where('kpis.targetable_type', '=', 'App\User');
            })
            ->leftJoin('profile_groups as pg', function ($join) {
                $join->on('pg.id', '=', 'kpis.targetable_id')
                    ->where('kpis.targetable_type', '=', 'App\ProfileGroup');
            })
            ->leftJoin('position as p', function ($join) {
                $join->on('p.id', '=', 'kpis.targetable_id')
                    ->where('kpis.targetable_type', '=', 'App\Position');
            })
            ->leftJoin('kpi_items as ki', 'ki.kpi_id', '=', 'kpis.id')
            ->leftJoin('users as updater', 'updater.id', '=', 'kpis.updated_by')
            ->leftJoin('users as creator', 'creator.id', '=', 'kpis.created_by')
            ->orWhere(function ($query) use ($searchWord) {
                $query->where('u.name', 'LIKE', "%$searchWord%")
                    ->orWhere('u.last_name', 'LIKE', "%$searchWord%");
            })
            ->orWhere(function ($query) use ($searchWord) {
                $query->where('updater.name', 'LIKE', "%$searchWord%")
                    ->orWhere('updater.last_name', 'LIKE', "%$searchWord%");
            })
            ->orWhere(function ($query) use ($searchWord) {
                $query->where('creator.name', 'LIKE', "%$searchWord%")
                    ->orWhere('creator.last_name', 'LIKE', "%$searchWord%");
            })
            ->orWhere('pg.name', 'LIKE', "%$searchWord%")
            ->orWhere('p.position', 'LIKE', "%$searchWord%")
            ->orWhere('ki.name', 'LIKE', "%$searchWord%")
            ->distinct();
    }
}