<?php

namespace App\Service\Courses;

use Illuminate\Http\Request;
use App\Models\Course;
use App\Models\CourseItemModel;
use App\Models\CourseResult;
use App\Models\TestBonus;
use App\Models\TestQuestion;
use App\Models\TestResult;
use Carbon\Carbon;

class CourseResultService
{
    
    /**
     * Nullify results for course of user
     * 
     * @param int $course_id
     * @param int $user_id
     * 
     * @return void
     */
    public function nullify(int $course_id, int $user_id) : void
    {
        // nullify CourseResult
        CourseResult::query()
                    ->where('user_id', $user_id)
                    ->where('course_id', $course_id)
                    ->update([
                        'progress'        => 0,
                        'status'          => CourseResult::INITIAL,
                        'points'          => 0,
                        'started_at'      => null,
                        'ended_at'        => null,
                        'weekly_progress' => null,
                    ]);

        // find course items            
        $course = Course::with('items')->find($course_id);

        foreach ($course->items as $key => $item) {
            
            // if has delete relatives
            // else do nothing

            $model = $item->model();
         
            if($model) {

                /**
                 * return ids of BookSegment KnowBase Video according to model
                 */
                $model_ids = $model->getOrder(); 

                /**
                 * return 1 2 3
                 */
                $type = CourseItemModel::getType($item->item_model); 
                
                /**
                 * find test_questions model
                 */
                $question_model = '';
                if($item->item_model == 'App\Models\Books\Book') {
                    $question_model = 'App\Models\Books\BookSegment';
                }
                if($item->item_model == 'App\KnowBase') {
                    $question_model = 'App\KnowBase';
                }
                if($item->item_model == 'App\Models\Videos\VideoPlaylist') {
                    $question_model = 'App\Models\Videos\Video';
                }

                /**
                 * delete course_item_model: info about pass part of course_item
                 */
                CourseItemModel::where('user_id', auth()->id())
                                ->where('type', $type)
                                ->where('course_item_id', $item->id)
                                ->where('user_id', $user_id)
                                ->whereIn('item_id', $model_ids)
                                ->select('id')
                                ->delete();
 
                /**
                 * Delete test results
                 * 
                 * find questions ids
                 * test result delete
                 */
                $test_questions = TestQuestion::query()
                                ->select('id')
                                ->where('testable_type', $question_model)
                                ->whereIn('testable_id', $model_ids)
                                ->get()
                                ->pluck('id')
                                ->toArray();

                TestResult::whereIn('test_question_id', $test_questions)
                            ->where('user_id', $user_id)
                            ->where('course_item_model_id',  $course_id)
                            ->delete();

                /**
                 *  Bonuses for tests delete
                 */
                TestBonus::where('user_id', $user_id)
                            ->whereYear('date', Carbon::now()->year)
                            ->whereMonth('date', Carbon::now()->month)
                            ->delete();

            }
 
           

        }
    }

    /**
     * Get course with items with results
     * 
     * @param int $course_id
     * @param int $user_id
     * 
     * @return array
     */
    public function getCourseWithResults(int $course_id, $user_id) : array
    {
        
    }

}