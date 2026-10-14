<?php

namespace App\Repositories;

use App\Models\TestResult as Model;

/**
* Класс для работы с Repository.
*/
class TestResultRepository extends CoreRepository
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
     * @param $testQuestionIds
     * @return mixed
     */
    public function getResults($userId, $testQuestionIds)
    {
        return $this->model()->where('user_id', $userId)->whereIn('test_question_id', $testQuestionIds);
    }
}