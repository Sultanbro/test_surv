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
        return $this->model()->join('kpis as k', 'k.id', '=', 'kpi_items.kpi_id')->where('kpi_items.id', $id)->first();
    }
}