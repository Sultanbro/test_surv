<?php

namespace App\Repositories;

use App\Models\Kpi\Bonus;
use App\Models\Kpi\Bonus as Model;

class KpiBonusRepository extends CoreRepository
{
    protected function getModelClass(): string
    {
        return Model::class;
    }

    /**
     * @param array $data
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model
     */
    public function saveNewBonus(array $data)
    {
        return Bonus::query()->create($data);
    }
}