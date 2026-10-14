<?php

namespace App\Service\Courses;

use App\Models\CourseItemModel;
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
                (new CourseResultRepository)->setIsRegressed($data['course_id'], $data['user_id']);

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
     * @param int $courseId
     * @param int $userId
     * @return void
     */
    protected function resetTestResult(int $courseId, int $userId): void
    {
        $items = (new CourseRepository)->getCourseItems($courseId)->pluck('id')->toArray();
        $courseItemModelIds = CourseItemModel::query()->where('user_id', $userId)->whereIn('course_item_id', $items)->get()->pluck('id')->toArray();

        TestResult::query()->where('user_id', $userId)->whereIn('course_item_model_id', $items)->delete();
        $this->deleteFromCourseItemModels($courseItemModelIds);
    }

    /**
     * @param array $ids
     * @return void
     */
    protected function deleteFromCourseItemModels(array $ids): void
    {
        CourseItemModel::query()->whereIn('id', $ids)->delete();
    }

    /**
     * Удалить записи из таблицы test_bonuses.
     *
     * @param int $courseId
     * @param int $userId
     * @return void
     */
    protected function resetTestBonus(int $courseId, int $userId): void
    {
        $items = (new CourseRepository)->getCourseItems($courseId)->pluck('id')->toArray();
        TestBonus::query()->where('user_id', $userId)->whereIn('course_item_id', $items)->delete();
    }
}