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
use App\KnowBase;

class CourseController extends Controller
{
    public function index(Request $request)
    {   
        View::share('menu', 'courses');
        View::share('link', 'faq');

        return view('surv.courses');
    }

    public function uploadImage(Request $request) {
        $course = Course::find($request->course_id);
        if($course) {
            $folder = 'courses';
            $filename = auth()->user()->ID . '_'.time().'_'. $request->file('file')->getClientOriginalName();
            $path = \Storage::putFileAs(
                'public/' . $folder, $request->file('file'), $filename
            );

            $end_path = '/storage/'. $folder . '/'. $filename;;
            $course->img = $end_path;
            $course->save();
            return [
                'img' => $end_path
            ];
        }
        
    }

    public function get(Request $request)
    {   


        return [
            'courses' => Course::with('items')->get()
        ];
    }

    public function save(Request $request)
    {   
        $course = Course::find($request->course['id']);

        if($course) {
            $course->name = $request->course['name'];
            $course->text = $request->course['text'];
            $course->save();
        }

        $ids = [];
        foreach($request->course['items'] as $index => $item) {
            if($item && array_key_exists('id', $item)) {
                array_push($ids, $item['id']);
            }
        }
        
        CourseItem::whereNotIn('id', $ids)->where('course_id', $request->course['id'])->delete();

        foreach($request->course['items'] as $index => $item) {
            if($item == null) continue;
            $ci = CourseItem::where('item_model', $item['item_model'])
                ->where('course_id', $request->course['id'])
                ->where('item_id', $item['item_id'])
                ->first();

            $arr = [
                'course_id' => $request->course['id'],
                'item_id' => $item['item_id'],
                'item_model' => $item['item_model'],
                'order' => $index++,
                'title' => $item['title'],
            ];

            if($ci) {
                $ci->update($arr);
            } else {
                CourseItem::create($arr);
            }
        }
    }


    public function getItem(Request $request)
    {   
        $all_items = [];


        $books = Book::get();
        $videos = VideoPlaylist::get();
        $kbs = KnowBase::whereNull('parent_id')->get();
        
        foreach($books as $book) {
            array_push($all_items, [
                'item_id' => $book->id,
                'title' => 'Книга: ' .$book->title,
                'item_model'=> 'book'
            ]);
        }

        foreach($videos as $video) {
            array_push($all_items, [
                'item_id' => $video->id,
                'title' => 'Видео: ' .$video->title,
                'item_model'=> 'video'
            ]);
        }

        foreach($kbs as $kb) {
            array_push($all_items, [
                'item_id' => $kb->id,
                'title' => 'БЗ: ' . $kb->title,
                'item_model'=> 'kb'
            ]);
        }

        return [
            'course' => Course::with('items')->find($request->id),
            'all_items' => $all_items,
        ];
    }

    public function create(Request $request)
    {
        return Course::create([
            'name' => $request->name,
        ]);
    }

    public function delete(Request $request) {
        $course = Course::find($request->id);

        if($course) {
            $course->delete();
        }
    }   

    public function myCourses(Request $request) {
        View::share('menu', 'mycourse');
        return view('admin.mycourse');
    }   
    
    public function getMyCourse(Request $request) {
        $user_id = auth()->user()->ID;
        $active_course = CourseResult::where('user_id', $user_id)->whereIn('status', [0,2])->orderBy('status', 'desc')->first();
        
        // $test_results = TestResult::where('user_id', $user_id)->user_id

        if($active_course) {
            //$course_items = $active_course ? 
        } else {
            $CourseProgress = CourseProgress::where('user_id', $user_id)->where()->get();
        }

        
        
        return $active_course;
    }   
}
