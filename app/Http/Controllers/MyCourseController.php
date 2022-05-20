<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use App\Models\Course;
use App\Models\CourseItem;
use App\Models\CourseResult;
use App\Models\CourseProgress;
use App\Models\Videos\VideoPlaylist;
use App\Models\Books\Book;
use App\Models\Videos\Video;
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
        $user_id = auth()->user()->ID;
        $courses = CourseResult::where('user_id', $user_id)
            ->whereIn('status', [0,2])
            ->orderBy('status', 'desc')
            ->with('course')
            ->get();
        
        return [
            'courses' => $courses,
        ];
    }   

    public function getMyCourse(Request $request) {
        $user_id = auth()->user()->ID;
       
        $active_course = CourseResult::where('user_id', $user_id)
            ->whereIn('status', [0,2])
            ->orderBy('status', 'desc')
            ->with('course')
            ->first();
        
        $course = Course::with('items')->find($active_course->course_id);

        $items = [];
        if($course) {
            $no_active = true;
            foreach ($course->items as $key => $course_item) {
                $item = $this->getCourseItem($course_item , $no_active);
                if($item) array_push($items, $item);
                
            }
        } 

        return [
            'course' => $active_course,
            'items' => $items,
        ];
    }   


    private function getCourseItem(CourseItem $course_item, &$no_active)
    {
        $user_id = auth()->user()->ID;

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
            
            foreach ($steps as $key => $step) {
        
                $fp = $progresses->where('item_id', $step['id'])->first();
                $steps[$key]['status'] = $fp ? $fp->status: 0;
                $steps[$key]['type'] = 'kb';
               
                if(!$fp) $statusOfCourse = CourseResult::ACTIVE;
            }
            
        }

        if($course_item->item_model == 'App\Models\Videos\Video') {
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
