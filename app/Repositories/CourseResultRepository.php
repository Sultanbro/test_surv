<?php

namespace App\Repositories;

use App\Models\CourseResult as Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class CourseResultRepository extends CoreRepository
{
    protected function getModelClass(): string
    {
        return Model::class;
    }

    /**
     * @param int $courseId
     * @param int $userId
     * @return mixed
     */
    public function resetResult(
        int $courseId,
        int $userId
    )
    {
        return $this->model()->where([
            ['course_id', $courseId],
            ['user_id', $userId]

        ])->update([
            'status'    => Model::INITIAL,
            'progress'  => 0,
            'points'    => 0,
            'weekly_progress' => null,
            'ended_at'  => null
        ]);
    }

    /**
     * Удаляем заработанные бонусы.
     *
     * @param int $userId
     * @param int $courseId
     * @param int $points
     * @param float $progress
     * @return void
     */
    public function removeItemPoints(
        int $userId,
        int $courseId,
        int $points,
        float $progress
    )
    {
        $result = $this->model()->where([
            ['course_id', $courseId],
            ['user_id', $userId]

        ])->first();

        $result->points   -= $points;
        $result->progress -= round($progress * 100, 2);

        if ($result->progress < 0)
        {
            $result->progress = 0;
        }
        $result->save();
    }

    /**
     * @param int $courseId
     * @param int $userId
     * @return void
     */
    public function setIsRegressed(
        int $courseId,
        int $userId
    ): void
    {
        $this->model()->where('user_id', $userId)->where('course_id', $courseId)->update(
            [
            'is_regressed' => 1
            ]
        );
    }

    /**
     * Метод получает и массив id или один id и ищет по нему совпадение.
     *
     * @param int|array $ids
     * @param int $userId
     * @return Builder
     */
    public function getResultByCourseIds(
        int|array $ids,
        int $userId
    ): Builder
    {
        return $this->model()
            ->when(is_array($ids), fn($courseResults) => $courseResults->whereIn('course_id', $ids))
            ->when(is_int($ids), fn($courseResults) => $courseResults->where('course_id', $ids))
            ->where('user_id', $userId)
            ->orderBy('status', 'desc');
    }
}