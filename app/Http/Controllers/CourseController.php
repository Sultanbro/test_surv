<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use App\Models\Course;
use App\Models\CourseItem;
use App\Models\CourseResult;
use App\Models\CourseProgress;
use App\Models\CourseModel;
use App\Models\Videos\VideoPlaylist;
use App\Models\Books\Book;
use App\KnowBase;
use App\ProfileGroup;
use App\Position;
use App\User;
use Carbon\Carbon;
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

        if(!auth()->user()->can('courses_view')) {
            return redirect('/');
        }

        $course = Course::with('items', 'models')->get();

        return [
            'courses' => $course
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
        
        // elements of course
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

        // who starts the course
        CourseModel::where('course_id', $course->id)->delete();

        // if there one badge with 'ALL' name
        if(count($request->course['targets']) == 1 && $request->course['targets'][0]['type'] == 0) {
            CourseModel::create([
                'course_id' => $course->id,
                'item_id' => 0,
                'item_model' => 0,
            ]);
        } else {
            // no badge
            foreach($request->course['targets'] as $index => $target) {
      
                if($target['type'] == 1) $model = 'App\\User';
                if($target['type'] == 2) $model = 'App\\ProfileGroup';
                if($target['type'] == 3) $model = 'App\\Position';
    
                CourseModel::create([
                    'course_id' => $course->id,
                    'item_id' => $target['id'],
                    'item_model' => $model,
                ]);
            }
        }   

        

    }


    public function getItem(Request $request)
    {   

        $course = Course::with('items', 'models')->find($request->id);
        
        $targets = [];
        foreach ($course->models as $key => $target) {
            if($target->item_model == 'App\\ProfileGroup') {
                $model = ProfileGroup::find($target->item_id);

                if($model) {
                    $targets[] = [
                        "name" => $model->name,
                        "id" => $model->id,
                        "type" => 2,
                    ];
                }
            }

            if($target->item_model == 'App\\User') {
                $model = User::withTrashed()->find($target->item_id);

                if($model) {
                    $targets[] = [
                        "name" => $model->last_name . ' ' . $model->name,
                        "id" => $model->id,
                        "type" => 1,
                    ];
                }
            }

            if($target->item_model == 'App\\Position') {
                $model = Position::find($target->item_id);

                if($model) {
                    $targets[] = [
                        "name" => $model->position,
                        "id" => $model->id,
                        "type" => 3,
                    ];
                }
                
            }
        }

        $course->targets = $targets;
        
        $author = User::withTrashed()->find($course->user_id);
        $course->author =  $author ? $author->last_name . ' ' . $author->name : 'Неизвестный';
       
        $course->created =  Carbon::parse($course->created_at)->format('d.m.Y');

        return [
            'course' => $course,
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
