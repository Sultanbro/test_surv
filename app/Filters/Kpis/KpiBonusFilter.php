<?php

namespace App\Filters\Kpis;

use App\Models\Kpi\Bonus;
use Illuminate\Database\Eloquent\Builder;

class KpiBonusFilter
{
    protected Bonus $bonus;

    public function __construct()
    {
        $this->bonus = new Bonus;
    }

    /**
     * @param string $searchWord
     * @return Builder
     */
    public function globalSearch(
        string $searchWord
    ): Builder
    {
        return $this->bonus::targetJoins()
            ->orWhere('kpi_bonuses.text', 'LIKE', "%$searchWord%")
            ->orWhere('kpi_bonuses.title', 'LIKE', "%$searchWord%")
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
            ->distinct();
    }
}