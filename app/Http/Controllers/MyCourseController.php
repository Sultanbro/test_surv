<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use App\Models\Course;
use App\Models\CourseItem;
use App\Models\CourseResult;
use App\Models\TestResult;
use App\Models\TestBonus;
use App\Models\CourseProgress;
use App\Models\Videos\VideoPlaylist;
use App\Models\Books\Book;
use App\Models\Videos\Video;
use App\Models\CourseItemModel;
use App\Models\CourseModel;
use App\KnowBase;
use App\User;
use DB;

class MyCourseController extends Controller
{
    public function index(Request $request) {
        View::share('menu', 'mycourse');
        return view('admin.mycourse');
    }   
    
    public function getCourses(Request $request) {
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
    public function pass(Request $request) {
        $user_id = auth()->id();
        $model = CourseItemModel::where('user_id', $user_id)
            ->where('type', $request->type)
            ->where('item_id', $request->id)
            ->where('course_item_id', $request->course_item_id)
            ->first();

        if($model) {
            $model->status = 1;
            $model->save();
        } else {
            $model = CourseItemModel::create([
                'type' =>  $request->type,
                'item_id' =>  $request->id,
                'course_item_id' =>  $request->course_item_id,
                'user_id' => $user_id,
                'status' =>  1,
            ]);
        }
        
        
        $sum_bonus = 0;

        foreach ($request->questions as $key => $q) {
            if(isset($q['result'])) {

                $tr = TestResult::where('test_question_id', $q['id'])
                    ->where('user_id', $user_id)
                    ->where('course_item_model_id',  $q['result']['course_item_model_id'])
                    ->first();

                if($tr) {
                    $tr->answer =  $q['result']['answer'];
                    $tr->save();
                } else {    

                   // $tq = TestQuestion::find($q['id']);
                    $sum_bonus = $q['points'];

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

        if($sum_bonus > 0) {
            TestBonus::create([
                'date' => date('Y-m-d'),
                'user_id' => $user_id,
                'amount' => $sum_bonus,
            ]);
        } 

        return [
            'item_model' => $model,
        ];
    }   

    public function getMyCourse(Request $request) {
     
        if($request->id) {
            $course = $this->getCourseIfVisible($request->id);
        } else {
            $course = CourseResult::activeCourse();
        }
       

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

    private function getCourseIfVisible($id) {
        // prepare
        $user = auth()->user();
        $user_id = $user->id;
        $position_id = $user->position_id;

        $groups = $user->inGroups();
        $group_ids = [];
        foreach ($groups as $key => $group) {
            $group_ids[] = $group->id;
        }

        // find course
        $courses = CourseModel::where(function($query) use ($user_id) {
                $query->where('item_model', 'App\\User')
                    ->where('item_id', $user_id);
            })
            ->orWhere(function($query) use ($group_ids) {
                $query->where('item_model', 'App\\ProfileGroup')
                    ->whereIn('item_id', $group_ids);
            })
            ->orWhere(function($query) use ($position_id) {
                $query->where('item_model', 'App\\Position')
                    ->where('item_id', $position_id);
            })
            ->orWhere(function($query) {
                $query->where('item_model', 0)
                    ->where('item_id', 0);
            })
            ->get()
            ->pluck('course_id')
            ->toArray();

        $course = null;
        if(in_array($id, $courses)) {
            $course_result = CourseResult::where('user_id', $user_id)
                ->where('course_id', $id)
                //->whereIn('status', [1])
                ->first();
            if(!$course_result) {
                $course_result = CourseResult::create([
                    'course_id' => $id,
                    'status' => 2,
                    'progress' => 0,
                    'points' => 0, 
                    'started_at' => now(), 
                    'ended_at' => null, 
                    'user_id' => $user_id
                ]);
            }

     

            // img poster
            $course = Course::with('items')->find($course_result->course_id);

            if($course && $course->img != '' && $course->img != null) {
                $disk = \Storage::build([
                    'driver' => 's3',
                    'key' => 'O4493_admin',
                    'secret' => 'nzxk4iNukQWx',
                    'region' => 'us-east-1',
                    'bucket' => 'tenantbp',
                    'endpoint' => 'https://storage.oblako.kz:443',
                    'use_path_style_endpoint' => true,
                    'throw' => false,
                    'visibility' => 'public'
                ]);

                if($disk->exists($course->img)) {
                    $course->img = $disk->temporaryUrl(
                        $course->img, now()->addMinutes(360)
                    );
                }
            }
            
        }

        return $course;
    }

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
