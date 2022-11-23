<?php

namespace App\Service\Courses;

use App\Models\TestBonus;
use App\Models\TestResult;
use App\Repositories\CourseItemRepository;
use App\Repositories\CourseRepository;
use App\Repositories\CourseResultRepository;
use App\Service\Interfaces\CourseRegress as RegressInterface;
use Exception;
use Illuminate\Support\Facades\DB;

/**
 * Класс CourseRegress отвечает за обнуление бонусов для курсов.
 */
class CourseRegress implements RegressInterface
{
    /**
     * @throws Exception
     */
    public function regress(array $data)
    {
        try {
            DB::transaction(function () use ($data) {
                (new CourseResultRepository)->resetResult($data['course_id'], $data['user_id']);

                $this->resetTestResult($data['course_id'], $data['user_id']);
                $this->resetTestBonus($data['course_id'], $data['user_id']);
            });
        }catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * Удалить записи из таблицы test_results.
     *
     * @param $courseId
     * @param $userId
     * @return void
     */
    protected function resetTestResult($courseId, $userId): void
    {
        $items = (new CourseRepository)->getCourseItems($courseId)->pluck('id')->toArray();
        TestResult::query()->where('user_id', $userId)->whereIn('course_item_model_id', $items)->delete();
    }

    /**
     * Удалить записи из таблицы test_bonuses.
     *
     * @param $courseId
     * @param $userId
     * @return void
     */
    protected function resetTestBonus($courseId, $userId): void
    {
        $items = (new CourseRepository)->getCourseItems($courseId)->pluck('id')->toArray();
        TestBonus::query()->where('user_id', $userId)->whereIn('course_item_id', $items)->delete();
    }
}