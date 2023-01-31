<?php

namespace App\Repositories;

use App\Models\WorkChartModel as Model;

/**
* Класс для работы с Repository.
*/
class WorkChartRepository extends CoreRepository
{
    /**
     * Здесь используется модель для работы с Repository {{ App\Models\{name} }}
     *
     * @return string
     */
    protected function getModelClass()
    {
        return Model::class;
    }
}