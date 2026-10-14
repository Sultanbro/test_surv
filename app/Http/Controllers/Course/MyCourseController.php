<?php

namespace App\Http\Controllers\Course;

use App\Http\Controllers\Controller;
use App\KnowBase;
use App\Models\Books\Book;
use App\Models\Books\BookSegment;
use App\Models\Course;
use App\Models\CourseItem;
use App\Models\CourseItemModel;
use App\Models\CourseProgress;
use App\Models\CourseResult;
use App\Models\TestBonus;
use App\Models\TestResult;
use App\Models\Videos\Video;
use App\Models\Videos\VideoPlaylist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

class MyCourseController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {

        View::share('menu', 'mycourse');
        return view('admin.mycourse');
    }

    /**
     *  get all courses of auth user
     *  this method is hidden for non-admin users
     */
    public function getCourses(Request $request)
    {
        $user_id = auth()->user()->id;
        $course_ids = CourseResult::where('user_id', $user_id)
            //->whereIn('status', [0,2])
            ->orderBy('status', 'desc')
            ->with('course')
            ->has('course')
            ->get(['course_id'])
            ->pluck('course_id')
            ->toArray();

        $courses = Course::whereIn('id', $course_ids)
            ->with('course_results', function ($query) use ($user_id) {
                $query->where('user_id', $user_id);
            })
            ->orderBy('order', 'asc')
            ->get();

        return [
            'courses' => $courses,
        ];
    }

    /**
     * Save passed course element to user
     * @param Request $request
     *
     * @return [type]
     */
    public function pass(Request $request)
    {
        $user_id = auth()->id();
        $courseItemId = $request->input('course_item_id');

        /**
         * save Course item model
         */
        $model = CourseItemModel::where('user_id', $user_id)
            ->where('type', $request->type)
            ->where('item_id', $request->id)
            ->where('course_item_id', $request->course_item_id)
            ->first();

        if ($model) {
            $model->status = 1;
            $model->save();
        } else {
            $model = CourseItemModel::create([
                'type' => $request->type,
                'item_id' => $request->id,
                'course_item_id' => $request->course_item_id,
                'user_id' => $user_id,
                'status' => 1,
            ]);
        }

        /**
         * save questions answers
         */
        $sum_bonus = 0;

        foreach ($request->questions as $key => $q) {
            if (isset($q['result'])) {

                $tr = TestResult::where('test_question_id', $q['id'])
                    ->where('user_id', $user_id)
                    ->where('course_item_model_id', $q['result']['course_item_model_id'])
                    ->first();

                if ($tr) {
                    $tr->answer = $q['result']['answer'];
                    $tr->save();
                } else {
                    // $tq = TestQuestion::find($q['id']);
                    if ($q['success']) $sum_bonus += $q['points'];

                    TestResult::create([
                        'test_question_id' => $q['result']['test_question_id'],
                        'answer' => $q['result']['answer'],
                        'status' => $q['result']['status'],
                        'user_id' => $user_id,
                        'course_item_model_id' => $q['result']['course_item_model_id'],
                    ]);
                }
            }
        }

        /**
         * save bonuses
         */
        if ($sum_bonus > 0) {

            // get item for what user has got bonus
            $item = null;
            $type = '';
            if ($request->type == 1) {
                $bs = BookSegment::find($request->id);
                if ($bs) {
                    $item = Book::find($bs->book_id);
                    $type = 'За чтение: ';
                }
            }

            if ($request->type == 2) {
                $video = Video::find($request->id);
                if ($video) {
                    $item = VideoPlaylist::find($video->playlist_id);
                    $type = 'За обучение по видео: ';
                }
            }

            if ($request->type == 3) {
                $item = KnowBase::getTopParent($request->id);
                if ($item) {
                    $type = 'За ответы в Базе знаний: ';
                }
            }

            //save
            TestBonus::query()->make([
                'date' => date('Y-m-d'),
                'user_id' => $user_id,
                'amount' => $sum_bonus,
                'comment' => $item ? $type . $item->title : 'За обучение',
                'course_item_id' => $courseItemId
            ]);
        }

        /**
         * count progress and weekly_progress
         * save in CourseResult
         */
        if ($request->course_item_id != 0) {
            // count progress
            $completed_stages = $request->completed_stages;

            $count_progress = $request->all_stages > 0 ? round($completed_stages / $request->all_stages * 100) : 0;
            $course_finished = false;
            if ($completed_stages >= $request->all_stages) $course_finished = true;
            if ($count_progress > 100) $count_progress = 100;

            // save course result for report

            $model = 0;
            if ($request->type == 1) $model = 'App\Models\Books\BookSegment';
            if ($request->type == 2) $model = 'App\Models\Videos\Video';
            //if($request->type == 3) $model = 'App\KnowBase';

            $course_item = CourseItem::where('id', $request->course_item_id)->first();


            $cr = $course_item ? CourseResult::where('course_id', $course_item->course_id)
                ->where('user_id', $user_id)
                ->first() : null;

            if ($cr) {
                if ($cr->status == CourseResult::INITIAL) $cr->status = CourseResult::ACTIVE;
                if ($cr->started_at == null) $cr->started_at = now();

                $cr->points += $sum_bonus;
                $cr->progress = $count_progress;

                // week progress
                $wp = $cr->weekly_progress;
                if ($wp == null) $wp = [];

                $sum = 1;
                if (array_key_exists(date('Y-m-d'), $wp)) {
                    $sum += (int)$wp[date('Y-m-d')];
                }
                $wp[date('Y-m-d')] = $sum;
                $cr->weekly_progress = $wp;


                //
                if ($course_finished) {
                    $cr->status = CourseResult::COMPLETED;
                    $cr->ended_at = now();
//                    event(new TrackCourseItemFinishedEvent($course_item->course, $user_id));
                }

                $cr->save();

            }
        }

        return [
            'item_model' => $model,
        ];
    }

    /**
     * get course
     */
    public function getMyCourse(Request $request): array
    {
        $course = CourseResult::activeCourse($request->id);

        $all_stages = 0;
        $completed_stages = 0;
        $items = [];

        if ($course) {
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

        if ($course_item->item_model == 'App\KnowBase') {

            // TODO
            $kb = KnowBase::with('children', 'questions')->where('id', $course_item->item_id)->first();

            if (!$kb) return null;

            $title = $kb->title;
            KnowBase::getArray($steps, $kb);
        }

        if ($course_item->item_model == 'App\Models\Videos\VideoPlaylist') {
            $pl = VideoPlaylist::with('videos')->find($course_item->item_id);
            if (!$pl) return null;

            $title = $pl->title;

            foreach ($pl->videos as $key => $video) {
                $fp = $progresses->where('item_id', $video->id)->first();
                $steps[] = [
                    'id' => $video->id,
                    'title' => $video->title,
                    'questions' => $video->questions,
                    'status' => $fp ? $fp->status : 0,
                    'links' => $video->links,
                    'type' => 'video',
                ];

                if (!$fp) $statusOfCourse = CourseResult::ACTIVE;
            }
        }

        if ($course_item->item_model == 'App\Models\Books\Book') {
            $book = Book::with('questions')->find($course_item->item_id);

            if (!$book) return null;

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
                    'status' => $fp ? $fp->status : 0
                ];
            }

            $fp = $progresses->where('item_id', $course_item->item_id)->first();
            if (!$fp) $statusOfCourse = CourseResult::ACTIVE;
        }

        $active = false;
        if ($no_active && $statusOfCourse == CourseResult::ACTIVE) {
            $no_active = false;
            $active == true;
        } else if ($statusOfCourse == CourseResult::ACTIVE) {
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
