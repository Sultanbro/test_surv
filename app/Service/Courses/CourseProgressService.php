<?php

namespace App\Service\Courses;

use App\DTO\CourseProgressDTO;
use App\Exceptions\NotResultsException;
use App\Models\CourseItem;
use App\Models\CourseItemModel;
use App\Models\TestBonus;
use App\Models\TestQuestion;
use App\Repositories\TestQuestionRepository;
use App\Repositories\TestResultRepository;
use Illuminate\Support\Facades\Log;

/**
* Класс для работы с Service.
*/
class CourseProgressService
{
    const TYPES = [
        'App\Models\Videos\VideoPlaylist'   => 'App\Models\Videos\Video',
        'App\Models\Books\Book'             => 'App\Models\Books\Book',
        'App\KnowBase'                      => 'App\Knowbase'
    ];

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
                $type = self::TYPES[$item->item_model];
                $orderIds = $item->element ? $item->element->getOrder() : [];

                $item->test_questions_points = $this->testQuestionTotalPoints($orderIds, $type);
                $item->test_results = $this->testResults($userId, $this->testQuestionIds($orderIds, $type));

                $bonuses = TestBonus::query()
                    ->selectRaw("SUM(amount) as sum")
                    ->where('course_item_id', $item->id)
                    ->where('user_id', $userId)
                    ->first()
                    ->sum;
                    
                $item->bonuses = $bonuses ?? 0;

                unset($item->element);
                return $item;
            });
    }

    /**
     * @param $ids
     * @param $type
     * @return mixed
     */
    private function testQuestionTotalPoints($ids, $type)
    {
        return (new TestQuestionRepository)->getQuestions($ids, $type)->sum('points');
    }

    /**
     * @param $ids
     * @param $type
     * @return array
     */
    private function testQuestionIds($ids, $type): array
    {
        return(new TestQuestionRepository)->getQuestions($ids, $type)->get()->pluck('id')->toArray();
    }

    /**
     * @param $userId
     * @param $testQuestionIds
     * @return mixed
     */
    private function testResults($userId, $testQuestionIds)
    {
        return (new TestResultRepository)->getResults($userId, $testQuestionIds)->get();
    }
}