<?php

namespace App\Http\Controllers\CourseV2;

use App\Http\Requests\CoursesV2\CreateCourseGradeV2Request;
use App\Http\Requests\CoursesV2\SaveUserCourseItemProgressRequest;
use App\Http\Resources\CoursesV2\MyCourseResource;
use App\Service\CourseV2\MyCourseV2Service;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use App\Models\CourseV2;
use App\Models\CourseItem;
use App\Models\CourseResult;
use App\Models\CourseProgress;
use App\Models\Videos\VideoPlaylist;
use App\Models\Books\Book;
use App\KnowBase;
use App\Http\Controllers\Controller;

class MyCourseV2Controller extends Controller
{
    public function index() {

        View::share('menu', 'mycourse');
        return view('admin.mycourse');
    }

    /**
     *  get all courses of auth user
     *  this method is hidden for non-admin users
     */
    public function getAll(MyCourseV2Service $service): JsonResponse
    {
        return $this->response(
            message: self::SUCCESS_MESSAGE,
            data: $service->getCourses()
        );
    }

    public function getOne(CourseV2 $course, MyCourseV2Service $service): JsonResponse
    {
        return $this->response(
            message: self::SUCCESS_MESSAGE,
            data: new MyCourseResource($course)
        );
    }


    public function assessCourseAndCurator(CreateCourseGradeV2Request $request, MyCourseV2Service $service): JsonResponse
    {
        return $this->response(
            message: self::SUCCESS_MESSAGE,
            data: $service->storeAssessment($request->toDto())
        );
    }

    /**
     * Save passed course element to user
     * @param SaveUserCourseItemProgressRequest $request
     * @param MyCourseV2Service $service
     * @return JsonResponse [type]
     */
    public function pass(SaveUserCourseItemProgressRequest $request, MyCourseV2Service $service): JsonResponse
    {
        return $this->response(
            message: self::SUCCESS_MESSAGE,
            data: $service->passV2($request->validated())
        );
    }

    /**
     * get course
     */
    public function getMyCourse(Request $request) : array
    {
        $course = CourseResult::activeCourse($request->id);

        $all_stages = 0;
        $completed_stages = 0;
        $items = [];

        if($course) {
            $items = $course->setCheckpoint($course->items);

            foreach ($items as $key => $item) {
                $all_stages += $item->all_stages;
                $completed_stages += $item->completed_stages;
            }
        }

        return [
            'course' => $course,
            'items' => $items,
            'all_stages' => $all_stages,
            'completed_stages' => $completed_stages
        ];
    }

    /**
     * IDK WTF is that for
     */
    private function getCourseItem(CourseItem $course_item, &$no_active)
    {
        $user_id = auth()->user()->id;

        $steps = [];

        $title = 'Этап';

        $progresses = CourseProgress::where('user_id', $user_id)
            ->where('item_model', $course_item->item_model)
            ->get();

        $statusOfCourse = 1;

        if($course_item->item_model == 'App\KnowBase') {

            // TODO
            $kb = KnowBase::with('children','questions')->where('id', $course_item->item_id)->first();

            if(!$kb) return null;

            $title = $kb->title;
            KnowBase::getArray($steps, $kb);
        }

        if($course_item->item_model == 'App\Models\Videos\VideoPlaylist') {
            $pl = VideoPlaylist::with('videos')->find($course_item->item_id);
            if(!$pl) return null;

            $title = $pl->title;

            foreach ($pl->videos as $key => $video) {
                $fp = $progresses->where('item_id', $video->id)->first();
                $steps[] = [
                    'id' => $video->id,
                    'title' =>  $video->title,
                    'questions' => $video->questions,
                    'status' => $fp ? $fp->status: 0,
                    'links' => $video->links,
                    'type' => 'video',
                ];

                if(!$fp) $statusOfCourse = CourseResult::ACTIVE;
            }
        }

        if($course_item->item_model == 'App\Models\Books\Book') {
            $book = Book::with('questions')->find($course_item->item_id);

            if(!$book) return null;

            $title = $book->title;

            $pages = $book->questions->groupBy('page');

            $steps[] = [
                'id' => $course_item->item_id,
                'title' => 'Введение',
                'type' => 'book',
                'link' => $book->link,
                'questions' => [],
                'status' => 1
            ];

            foreach ($pages as $page => $test) {

                $fp = $progresses->where('item_id', $page)->first();

                $steps[] = [
                    'id' => $course_item->item_id,
                    'title' => 'Вопросы на стр. ' . $page,
                    'type' => 'book',
                    'questions' => $test,
                    'status' => $fp ? $fp->status: 0
                ];
            }

            $fp = $progresses->where('item_id', $course_item->item_id)->first();
            if(!$fp) $statusOfCourse = CourseResult::ACTIVE;
        }

        $active = false;
        if($no_active && $statusOfCourse == CourseResult::ACTIVE) {
            $no_active = false;
            $active == true;
        } else if($statusOfCourse == CourseResult::ACTIVE) {
            $statusOfCourse = CourseResult::INITIAL;
        }



        return [
            'id' => $course_item->id,
            'item_id' => $course_item->item_id,
            'item_model' => $course_item->item_model,
            'status' => $statusOfCourse,
            'active' => $active,
            'steps' => $steps,
            'title' => $title,
        ];
    }
}
