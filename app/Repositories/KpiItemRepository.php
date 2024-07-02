<?php

namespace App\Repositories;

use App\Models\Kpi\KpiItem as Model;
use Illuminate\Database\Eloquent\Builder;

class KpiItemRepository extends CoreRepository
{
    /**
     * @return string
     */
    protected function getModelClass()
    {
        return Model::class;
    }

    /**
     * @param $id
     * @return mixed
     */
    public function getById($id)
    {
        return $this->model()->findOrFail($id);
    }

    /**
     * @param $id
     * @param $date
     * @return Builder|\Illuminate\Database\Eloquent\Model|object|null
     */
    public function joinKpiItemsWithKpi($id, $date)
    {
        return $this->model()
            ->with(
                [
                    'histories' => fn($q) => $q->whereMonth('created_at', $date->month)->whereYear('created_at', $date->year),
                    'kpi'
                ]
            )->where('id', $id)->first();
    }
}