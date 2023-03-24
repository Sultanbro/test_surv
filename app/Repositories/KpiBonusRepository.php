<?php

namespace App\Repositories;

use App\Models\Kpi\Bonus;
use App\Models\Kpi\Bonus as Model;
use Illuminate\Database\Query\Builder;

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

    /**
     * @param int $userId
     * @param string $date
     * @return ?object
     */
    public function getBonusByUserModelPerDate(
        int $userId,
        string $date
    ): ?object
    {
        return $this->model()->where([
            ['targetable_id',   '=', $userId],
            ['targetable_type', '=', 'App\User']
        ])->whereDate('created_at', $date);
    }
}