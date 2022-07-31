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

        if(!auth()->user()->can('courses_view')) {
            return redirect('/');
        }
        
        return view('surv.courses');
    }

    public function uploadImage(Request $request) {
        $course = Course::find($request->course_id);
        if($course) {

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

            if($course->img != '' && $course->img != null) {
                if($disk->exists($course->img)) {
                    $disk->delete($course->img);
                }
            }
            
            $links = $this->uploadFile('/courses', $request->file('file')); 
            $img_link = $links['temp'];
            $course->img = $links['relative'];

            $course->save();
            
            return [
                'img' => $img_link
            ];
        }
    }
    
    public function saveOrder(Request $request, $id = null)
    {

        $course = Course::find($request->id);
        if ($course) {
            $course->order = $request->order;
            $course->save();
        }

        $courses = Course::where('id', '!=', $request->id)
            ->orderBy('order', 'asc')
            ->get();

        $order = 0;
        foreach ($courses as $course) {
            if($order == $request->order) {
                $order++;
            } 
            $course->order = $order;
            $course->save();
            $order++;
        }

    }
    
        /**
     * Upload file to S3 and return relative link
     * @param String $path
     * @param mixed $file
     * 
     * @return array 
     * 
     * 'relative' => String
     * 'temp' => String
     */
    private function uploadFile(String $path, $file)
    {
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

        $extension = $file->getClientOriginalExtension();
        $originalFileName = $file->getClientOriginalName();
        $fileName = uniqid() . '_' . md5(time()) . '.' . $extension; // a unique file name

        $disk->putFileAs($path, $file, $fileName);

        $xpath = $path . '/' . $fileName;
        
        return [
            'relative' => $xpath,
            'temp' => $disk->temporaryUrl(
                $xpath, now()->addMinutes(360)
            )
        ];
    }
        
    
    public function get(Request $request)
    {   

        if(!auth()->user()->can('courses_view')) {
            return redirect('/');
        }

        $courses = Course::with('items', 'models')->orderBy('order', 'asc')->get();

        return [
            'courses' => $courses
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

        // elements of course
        $elements = [];
        foreach($request->course['elements'] as $index => $item) {
            if($item == null) continue;

            if($item['type'] == 1) $model = 'App\\Models\\Books\\Book';
            if($item['type'] == 2) $model = 'App\\Models\\Videos\\VideoPlaylist';
            if($item['type'] == 3) $model = 'App\\KnowBase';


            array_push($elements, [
                'item_id' => $item['id'],
                'item_model' => $model,
            ]);

            $ci = CourseItem::where('item_model', $model)
                ->where('course_id', $request->course['id'])
                ->where('item_id', $item['id'])
                ->first();

            $arr = [
                'course_id' => $request->course['id'],
                'item_id' => $item['id'],
                'item_model' => $model,
                'order' => $index++,
                'title' => $item['name'],
            ];

            if($ci) {
                $ci->update($arr);
            } else {
                CourseItem::create($arr);
            }
        }

        $elements = collect($elements);

        $ids = [];
        foreach($request->course['items'] as $index => $item) {
            if($elements->where('item_id', $item['item_id'])->where('item_model', $item['item_model'])->first() == null) {
                array_push($ids, $item['id']);
            }
        }
        
        // delete
        CourseItem::whereIn('id', $ids)->where('course_id', $request->course['id'])->delete();

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
        
        // img poster
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

        if($course->img != '' && $course->img != null) {
            if($disk->exists($course->img)) {
                $course->img = $disk->temporaryUrl(
                    $course->img, now()->addMinutes(360)
                );
            }
        }

        // targets
        $targets = [];



        // если указано Все проходят этот курс
        if(
            $course->models
                ->where('item_id', 0)
                ->where('item_model', 0)
                ->first()
        ) {
            $targets[] = [
                "name" => 'Все',
                "id" => 0,
                "type" => 0,
            ];
        } else {

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

        }
        
     

        $course->targets = $targets;
        
        // get course items

        $items = [];

   
        foreach ($course->items as $key => $target) {
            if($target->item_model == 'App\\Models\\Books\\Book') {
                $model = Book::find($target->item_id);

                if($model) {
                    $items[] = [
                        "name" => $model->title . ' - ' . $model->author,
                        "id" => $model->id,
                        "type" => 1,
                    ];
                }
            }

            if($target->item_model == 'App\\Models\\Videos\\VideoPlaylist') {
                $model = VideoPlaylist::withTrashed()->find($target->item_id);

                if($model) {
                    $items[] = [
                        "name" => $model->title,
                        "id" => $model->id,
                        "type" => 2,
                    ];
                }
            }

            if($target->item_model == 'App\\KnowBase') {
                $model = KnowBase::whereNull('parent_id')->find($target->item_id);

                if($model) {
                    $items[] = [
                        "name" => $model->title,
                        "id" => $model->id,
                        "type" => 3,
                    ];
                }
                
            }
        }
        
        $course->elements = $items;
        
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
            'user_id' => auth()->id()
        ]);
    }

    public function delete(Request $request) {
        $course = Course::find($request->id);

        if($course) {
            $course->delete();
        }
    }   

  
}
