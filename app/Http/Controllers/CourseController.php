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
use App\User;
use DB;

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
            $filename = auth()->user()->id . '_'.time().'_'. $request->file('file')->getClientOriginalName();
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

        foreach($request->course['users'] as $index => $user) {
            $cr = CourseResult::where('user_id', $user['ID'])->where('course_id', $request->course['id'])->first();
            if(!$cr) CourseResult::create([
                'user_id' => $user['ID'],
                'course_id' => $request->course['id'],
                'status' => CourseResult::INITIAL,
                'points' => 0
            ]);
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
                'item_model'=> 'App\Models\Books\Book'
            ]);
        }

        foreach($videos as $video) {
            array_push($all_items, [
                'item_id' => $video->id,
                'title' => 'Видео: ' .$video->title,
                'item_model'=> 'App\Models\Videos\Video'
            ]);
        }

        foreach($kbs as $kb) {
            array_push($all_items, [
                'item_id' => $kb->id,
                'title' => 'БЗ: ' . $kb->title,
                'item_model'=> 'App\KnowBase'
            ]);
        }

        $course = Course::with('items')->find($request->id);
        
        $course_users = CourseResult::where('course_id', $request->id)->get(['user_id'])->pluck('user_id')->toArray();
        $course->users = User::withTrashed()->whereIn('ID', $course_users)->get(['ID', DB::raw("CONCAT(NAME,' ',LAST_NAME) as EMAIL")]);

        $users = User::get(['ID', DB::raw("CONCAT(NAME,' ',LAST_NAME) as EMAIL")]);

        foreach($users as $user) {
            if($user->EMAIL == '') $user->EMAIL = 'x'; 
        }
        return [
            'course' => $course,
            'all_items' => $all_items,
            'users' => $users
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

  
}
