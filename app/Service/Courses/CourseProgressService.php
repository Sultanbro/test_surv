<?php

namespace App\Service\Courses;

use App\DTO\CourseProgressDTO;
use App\Exceptions\NotResultsException;
use App\Models\CourseItem;
use App\Models\CourseItemModel;
use App\Models\TestBonus;
use Illuminate\Support\Facades\Log;

/**
* Класс для работы с Service.
*/
class CourseProgressService
{

    public function __construct(public CourseProgressDTO $dto)
    {

    }

    /**
     * @return array
     * @throws NotResultsException
     */
    public function handle(): array
    {   
        try {
            return [
                'course'      => $this->dto->course,
                'courseItems' => $this->getCourseItems()
            ];
        } catch (NotResultsException $exception) {
            Log::error($exception->getMessage());

            throw new NotResultsException($exception->getMessage());
        }
    }

    /**
     * Получить этапы курса с прогрессом
     * 
     * @return \Illuminate\Support\Collection <CourseItem>
     */
    private function getCourseItems()
    {
        $userId = $this->dto->user->id;
        $courseId = $this->dto->course->id;

        return CourseItem::with('element')
            ->where('course_id', $courseId)
            ->get()
            ->map(function($item) use ($userId) {
                $item->element_id = $item->element ? $item->element->id : 0;
        
                $item->stages = $item->element ? count($item->element->getOrder()) : 0;
                $item->passed_stages = $item->element
                    ? (new CourseItemModel())->progress($userId, $item->id, $item->element)
                    : [];

                $item->bonuses = TestBonus::query()
                                    ->selectRaw("SUM(amount) as sum")
                                    ->where('course_item_id', $item->id)
                                    ->where('user_id', $userId)
                                    ->first()
                                    ->sum;

                unset($item->element);
                return $item;
            });
    }
}