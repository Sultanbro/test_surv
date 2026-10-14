<?php

namespace App\Filters\Kpis;

use App\Models\QuartalPremium;
use Illuminate\Database\Eloquent\Builder;

class QuarterPremiumFilter
{
    protected QuartalPremium $premium;

    public function __construct()
    {
        $this->premium = new QuartalPremium;
    }

    /**
     * @param string $searchWord
     * @return Builder
     */
    public function globalSearch(
        string $searchWord
    ): Builder
    {
        return $this->premium::targetJoins()
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
            ->orWhere('quartal_premiums.title', 'LIKE', "%$searchWord%")
            ->orWhere('quartal_premiums.text', 'LIKE', "%$searchWord%")
            ->distinct();
    }
}