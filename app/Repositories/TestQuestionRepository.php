<?php

namespace App\Repositories;

use App\Models\TestQuestion as Model;

/**
* Класс для работы с Repository.
*/
class TestQuestionRepository extends CoreRepository
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

    /**
     * @param $ids
     * @param $type
     * @return mixed
     */
    public function getQuestions($ids, $type)
    {
        return $this->model()->whereIn('testable_id', $ids)->where('testable_type', $type);
    }
}