<?php

namespace App\Repositories;

use App\Models\Kpi\KpiItem as Model;

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
     * @return mixed
     */
    public function joinKpiItemsWithKpi($id)
    {
        return $this->model()
            ->select('k.completed_100', 'k.completed_80', 'kpi_items.method', 'kpi_items.plan', 'kpi_items.share')
            ->join('kpis as k', 'k.id', '=', 'kpi_items.kpi_id')
            ->join('activities as a', 'kpi_items.activity_id', '=', 'a.id')
            ->where('kpi_items.id', $id)->first();
    }
}