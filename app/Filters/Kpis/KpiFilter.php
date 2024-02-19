<?php

namespace App\Filters\Kpis;

use App\Models\Kpi\Kpi;
use Illuminate\Database\Eloquent\Builder;


class KpiFilter
{
    protected Kpi $kpi;

    public function __construct()
    {
        $this->kpi = new Kpi;
    }

    /**
     * @param string $searchWord
     * @return Builder
     */
    public function globalSearch(
        string  $searchWord
    ): Builder
    {
        dd($searchWord);
        return $this->kpi::targetJoins()
            ->where(function ($query) use ($searchWord) {
                $query->where('u.name', 'LIKE', "%$searchWord%")
                    ->orWhere('u.last_name', 'LIKE', "%$searchWord%");
            })
//            ->orWhere(function ($query) use ($searchWord) {
//                $query->where('updater.name', 'LIKE', "%$searchWord%")
//                    ->orWhere('updater.last_name', 'LIKE', "%$searchWord%");
//            })
//            ->orWhere(function ($query) use ($searchWord) {
//                $query->where('creator.name', 'LIKE', "%$searchWord%")
//                    ->orWhere('creator.last_name', 'LIKE', "%$searchWord%");
//            })
//            ->orWhere('pg.name', 'LIKE', "%$searchWord%")
//            ->orWhere('p.position', 'LIKE', "%$searchWord%")
//            ->orWhere('ki.name', 'LIKE', "%$searchWord%")
            ->distinct();
    }
}