<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use App\Models\Course;
use App\Models\CourseItem;
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
            $course->save();
        }

        $ids = [];
        foreach($request->course['items'] as $index => $item) {
            if($item && array_key_exists('id', $item)) {
                array_push($ids, $item['id']);
            }
        }
        
        CourseItem::whereNotIn('id', $ids)->delete();

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
}
