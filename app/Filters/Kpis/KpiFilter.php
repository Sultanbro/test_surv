<?php

namespace App\Filters\Kpis;

use App\Models\Kpi\Kpi;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\JoinClause;


class KpiFilter
{
    protected Kpi $kpi;
    private ?Builder $query;

    public function __construct(?Builder $query = null)
    {
        $this->kpi = new Kpi;
        $this->query = $query;
    }

    /**
     * @param string $searchWord
     * @return Builder
     */
    public function globalSearch(
        string $searchWord
    ): Builder
    {
//        $query = $this->kpi::targetJoins($this->query);
        $table = $this->query->getModel()->getTable();
        return $this->query->select($table . '.*')
            ->leftJoin('kpiables as morph', function (JoinClause $join) use ($table) {
                $join->on('morph.kpi_id', '=', "$table.id");
            })
            ->leftJoin('users as u', function (JoinClause $join) use ($table) {
                $join->on('u.id', '=', "$table.targetable_id")
                    ->where("$table.targetable_type", '=', 'App\User');
                $join->orOn('u.id', '=', "morph.kpiable_id")
                    ->where("morph.kpiable_type", '=', 'App\User');
            })
            ->leftJoin('profile_groups as pg', function (JoinClause $join) use ($table) {
                $join->on('pg.id', '=', "$table.targetable_id")
                    ->where("$table.targetable_type", '=', 'App\ProfileGroup');
                $join->orOn('pg.id', '=', "morph.kpiable_id")
                    ->where("morph.kpiable_type", '=', 'App\ProfileGroup');
            })
            ->leftJoin('position as p', function (JoinClause $join) use ($table) {
                $join->on('p.id', '=', "$table.targetable_id")
                    ->where("$table.targetable_type", '=', 'App\Position');
                $join->orOn('p.id', '=', "morph.kpiable_id")
                    ->where("morph.kpiable_type", '=', 'App\Position');
            })
            ->leftJoin('kpi_items as ki', 'ki.kpi_id', '=', 'kpis.id')
            ->leftJoin('users as updater', 'updater.id', '=', "$table.updated_by")
            ->leftJoin('users as creator', 'creator.id', '=', "$table.created_by")
            ->where(function ($query) use ($searchWord) {
                $query->where(function ($query) use ($searchWord) {
                    $query->where('u.name', 'LIKE', "%$searchWord%")
                        ->orWhere('u.last_name', 'LIKE', "%$searchWord%");
                });
                $query->orWhere('pg.name', 'LIKE', "%$searchWord%");
                $query->orWhere('p.position', 'LIKE', "%$searchWord%");
                $query->orWhere('ki.name', 'LIKE', "%$searchWord%");
//            ->orWhere(function ($query) use ($searchWord) {
//                $query->where('updater.name', 'LIKE', "%$searchWord%")
//                    ->orWhere('updater.last_name', 'LIKE', "%$searchWord%");
//            })
//            ->orWhere(function ($query) use ($searchWord) {
//                $query->where('creator.name', 'LIKE', "%$searchWord%")
//                    ->orWhere('creator.last_name', 'LIKE', "%$searchWord%");
//            })
            })
            ->distinct();
    }
}