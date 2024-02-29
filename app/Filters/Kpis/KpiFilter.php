<?php

namespace App\Filters\Kpis;

use App\Models\Kpi\Kpi;
use Illuminate\Database\Eloquent\Builder;


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
        return $this->kpi::targetJoins($this->query)
            ->where(function ($query) use ($searchWord) {
                $query->where(function ($query) use ($searchWord) {
                    $query->where('u.name', 'LIKE', "%$searchWord%")
                        ->orWhere('u.last_name', 'LIKE', "%$searchWord%");
                });
                $query->orWhere('pg.name', 'LIKE', "%$searchWord%");
                $query->orWhere('p.position', 'LIKE', "%$searchWord%");
                $query->orWhere('ki.name', 'LIKE', "%$searchWord%");
//                $query->orWhere(function ($query) use ($searchWord) {
//                    $query->where('updater.name', 'LIKE', "%$searchWord%")
//                        ->orWhere('updater.last_name', 'LIKE', "%$searchWord%");
//                });
//                $query->orWhere(function ($query) use ($searchWord) {
//                    $query->where('creator.name', 'LIKE', "%$searchWord%")
//                        ->orWhere('creator.last_name', 'LIKE', "%$searchWord%");
//                });
            })
            ->distinct();
    }
}   