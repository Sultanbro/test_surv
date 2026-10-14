<?php

namespace App\Service\Courses;

use App\Http\Requests\CourseRegressRequest;
use App\Models\CourseItemModel;
use App\Models\TestBonus;
use App\Models\TestResult;
use App\Repositories\CourseItemRepository;
use App\Repositories\CourseResultRepository;
use App\Service\Interfaces\CourseRegress;
use Illuminate\Support\Facades\DB;

/**
 * Класс CourseRegress отвечает за обнуление бонусов для разделов курса.
 */
class CourseItemRegress implements CourseRegress
{
    /**
     * @param array $data
     * @return void
     */
    public function regress(array $data)
    {
        $amount = 0;
//            $this->getAmount($data['user_id'], $data['course_item_id']);
        $course = (new CourseItemRepository)->getCourse($data['course_item_id'])->id;
        $progress = $this->getProgress($data['course_item_id'], $data['user_id']);

        DB::transaction(function () use ($data, $amount, $course, $progress) {
            (new CourseResultRepository)->removeItemPoints($data['user_id'], $course, $amount, $progress);
            (new CourseResultRepository)->setIsRegressed($course, $data['user_id']);

            $this->deleteCourseItemModel($data['course_item_id'], $data['user_id']);
            $this->deleteItemFromTestResult($data['user_id'], $data['course_item_id']);
            $this->deleteItemFromTestBonuses($data['user_id'], $data['course_item_id']);
        });
    }

    /**
     * Получаем сумму бонусов за раздел.
     *
     * @param int $userId
     * @param int $courseItemId
     * @return int
     */
    protected function getAmount(int $userId, int $courseItemId): int
    {
        return TestBonus::query()->where([
            ['user_id', '=', $userId],
            ['course_item_id', '=', $courseItemId]
        ])->sum('amount');
    }

    /**
     * @param int $courseItemId
     * @param int $userId
     * @return float
     */
    protected function getProgress(int $courseItemId, int $userId): float
    {
        $course = (new CourseItemRepository)->getCourse($courseItemId);
        return CourseItemModel::query()->where('user_id', $userId)->where('course_item_id', $courseItemId)->count() / $course->stages;
    }

    protected function deleteCourseItemModel(int $courseItemId, int $userId)
    {
        CourseItemModel::query()->where('user_id', $userId)->where('course_item_id', $courseItemId)->delete();
    }

    /**
     * @param int $userId
     * @param int $courseItemId
     * @return void
     */
    protected function deleteItemFromTestResult(
        int $userId,
        int $courseItemId
    ): void
    {
        TestResult::query()->where([
            ['user_id', $userId],
            ['course_item_model_id', $courseItemId]
        ])->delete();
    }

    /**
     * @param int $userId
     * @param int $courseItemId
     * @return void
     */
    protected function deleteItemFromTestBonuses(
        int $userId,
        int $courseItemId
    ): void
    {
        TestBonus::query()->where([
            ['user_id', $userId],
            ['course_item_id', $courseItemId]
        ])->delete();
    }
}