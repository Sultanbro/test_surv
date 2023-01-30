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

    public function all():object
    {
        return $this->model()->all();
    }


    public function create($attribute):object
    {
        return $this->model()->query()->firstOrCreate([
            'name' => $attribute['name'],
            'time_beg' => $attribute['time_beg'],
            'time_end' => $attribute['time_end']], $attribute);
    }

    public function show($id):object
    {
        return $this->model()->query()->findOrFail($id);
    }

    public function update($attribute, $id):object
    {
        $model = $this->model()->query()->findOrFail($id);
        $model->update($attribute);
        return $model;
    }

    public function delete($id):bool
    {
        if ($this->model()->query()->findOrFail($id)) {
            return $this->model->find($id)->delete();
        }
        return false;
    }
}