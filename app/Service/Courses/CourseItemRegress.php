<?php

namespace App\Service\Courses;

use App\Http\Requests\CourseRegressRequest;
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
        $amount = $this->getAmount($data['user_id'], $data['course_item_id']);
        $course = (new CourseItemRepository)->getCourse($data['course_item_id'])->id;

        DB::transaction(function () use ($data, $amount, $course){
            (new CourseResultRepository)->removeItemPoints($data['user_id'], $course, $amount, $data['completed_stages']);

            $this->deleteItemFromTestResult($data['user_id'], $data['course_item_id']);
            $this->deleteItemFromTestBonuses($data['user_id'], $data['course_item_id']);
        });
    }

    /**
     * Получаем сумму бонусов за раздел.
     *
     * @param int $userId
     * @param int $courseItemId
     * @return int|mixed
     */
    protected function getAmount(int $userId, int $courseItemId)
    {
        return TestBonus::query()->where([
            ['user_id', '=', $userId],
            ['course_item_id', '=', $courseItemId]
        ])->sum('amount');
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