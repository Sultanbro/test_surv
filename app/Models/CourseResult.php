<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Auth;
use Carbon\Carbon;
use App\User;
use App\ProfileGroup;
use App\Models\Course;
use App\Models\CourseResult;
use App\Models\TestResult;
use App\Models\UserCourse;
use App\Models\CourseItem;
use App\Models\CourseModel;
use App\Models\TestQuestion;

class CourseResult extends Model
{
    protected $table = 'course_results';

    public $timestamps = true;

    protected $casts = [
        'weekly_progress' => 'array',
    ];

    protected $fillable = [
        'user_id',
        'course_id',
        'status',
        'progress', // 0 - 100
        'points', 
        'started_at', 
        'ended_at', 
        'weekly_progress'
    ];

    // status
    CONST INITIAL = 0;
    CONST COMPLETED = 1;
    CONST ACTIVE = 2;
    CONST CANCELED = 3;

    CONST STATUSES = [
        0 => 'Запланирован',
        1 => 'Завершил',
        2 => 'Начал',
        3 => 'Отменен',
    ];

    public static $courses;

    public function course()
    {
        return $this->belongsTo('App\Models\Course', 'course_id', 'id');
    }
    
    public static function getUsers($group_id, $date = null)
    {
        $user_ids = ProfileGroup::employees($group_id);
        $users = [];

        self::$courses = Course::get();

        foreach ($user_ids as $key => $user_id) {
            $user = User::withTrashed()
                ->with('course_results')
                ->with('course_results.course')
                ->find($user_id);

            if(!$user) continue;
            
            array_push($users, self::getUserItem($user, $date));
        }

        return [
            'items' => $users,
            'fields' => self::getUserFields()    
        ];
    }

    private static function getUserFields() {
        $arr = [];
        $arr[] = [
            'key' => 'name',
            'name' => 'Сотрудник',
            'class' => 'text-left'
        ];
        $arr[] = [
            'key' => 'status',
            'name' => 'Статус',
            'class' => 'text'
        ];
        $arr[] = [
            'key' => 'points',
            'name' => 'Заработано бонусов',
            'class' => 'text'
        ];
        $arr[] = [
            'key' => 'progress',
            'name' => 'Прогресс',
            'class' => 'text'
        ];
        $arr[] = [
            'key' => 'progress_on_week',
            'name' => 'Прогресс за 7 дней',
            'class' => 'text'
        ];
        $arr[] = [
            'key' => 'started_at',
            'name' => 'Дата начала',
            'class' => 'text'
        ];
        $arr[] = [
            'key' => 'ended_at',
            'name' => 'Дата завершения',
            'class' => 'text'
        ];
        
        return $arr;
    }
    
    private static function getUserItem($user, $date) {
        $arr = [];

        $uc = self::getUserCourses($user);
        $arr['courses'] = $uc['courses'];

        $arr['name'] = $user->last_name . ' ' . $user->name . ' (' . $user->course_results->count() .')';
        $arr['user_id'] = $user->id;



        $arr['status'] = $uc['totals']['status'] == 2 ? 'Начал' : 'Завершил';


        $arr['progress'] = $uc['totals']['progress'] . '%' ;
        $arr['progress_on_week'] = $uc['totals']['progress_on_week'] . '%' ;
        $arr['progress_number'] = $uc['totals']['progress'];  
        $arr['points'] = $uc['totals']['points'];
        $arr['expanded'] = false;
        $arr['started_at'] = $uc['totals']['started_at'];
        $arr['ended_at'] = $uc['totals']['ended_at'];
        
        return $arr;
    }

    private static function getUserCourses($user) {
        $arrx = [];

        $points = 0;
        $status = 2;
        $progress = 0;
        $progress_weekly = 0;
        $progress_count = 0;

        $first_date = $user->course_results->sort(function ($a, $b) {
            return strtotime($a->started_at) < strtotime($b->started_at);
        })->first();

        $last_date = $user->course_results->sort(function ($a, $b) {
            return strtotime($a->ended_at) < strtotime($b->ended_at);
        })->first();

        // get user courses
        $course_ids = self::getCourseIds($user->id);
        
        // order 
        foreach($user->course_results as $result) {
            $result->order = $result->course != null ? $result->course->order : 999;
        }

        foreach ($course_ids as $key => $course_id) {
            if(!$user->course_results->where('course_id', $course_id)->first()) {
                $cr = self::create([
                    'user_id' => $user->id,
                    'course_id' => $course_id,
                    'status' => self::INITIAL,
                    'progress' => 0, // 0 - 100
                    'points'=> 0, 
                    'started_at' => null, 
                    'ended_at' => null, 
                ]);
                $cr->order = $key;
                $user->course_results->push($cr);
            } 
        }

        $user->course_results = $user->course_results->sortBy('order');

        // do 
        $status = $user->course_results->where('status', 2)->first() ? 2 : 1;


        // do
        foreach($user->course_results as $result) {

            $course = self::$courses->where('id', $result->course_id)->first();
            if($course) {
                $arr = [];
                $arr['name'] = $course->name;

                $arr['status'] = self::STATUSES[$result->status];
                $arr['user_id'] = $user->id;

                // total progress
                $progress += $result->progress;
                $progress_count++;
                $arr['progress'] = $result->progress > 100 ? '100%' : $result->progress . '%';

                // weekly progress
                
                $stages_completed = $result->countWeeklyProgress();
                $weekly_progress = $stages_completed > 0 && $result->course != null && $result->course->stages > 0 ? round($stages_completed / $result->course->stages) : 0;
                $arr['progress_on_week'] = $weekly_progress;
                
                $progress_weekly += $weekly_progress;

                // points
                $points += $result->points;
                $arr['points'] = $result->points;

                // dates
                $arr['started_at'] = $result->started_at ? Carbon::parse($result->started_at)->format('d.m.Y') : '';
                $arr['ended_at'] =  $result->ended_at ? Carbon::parse($result->ended_at)->format('d.m.Y') : '';
                
                array_push($arrx, $arr);
            }
        }

        $total_progress = $progress_count > 0 ? round($progress / $progress_count) : 0;
        if($total_progress > 100) $total_progress = 100;
        
        $total_progress_weekly = $progress_count > 0 ? round($progress_weekly / $progress_count) : 0;
        if($total_progress_weekly > 100) $total_progress_weekly = 100;

        return [
            'courses' => $arrx,
            'totals' => [
                'points' => $points,
                'progress' => $total_progress,
                'progress_on_week' => $total_progress_weekly,
                'status' => $status,
                'started_at' => $first_date && $first_date->started_at ? Carbon::parse($first_date->started_at)->format('d.m.Y') : '',
                'ended_at' => $last_date && $last_date->ended_at ? Carbon::parse($last_date->ended_at)->format('d.m.Y') : '',
            ]
        ];
    }

    public function countWeeklyProgress() {
        $stages = 0;

        $date = Carbon::now()->addDay();

        $weekly_progress = collect($result->weekly_progress);
        
        for($i = 1; $i <= 7; $i++) {
            $day = $date->subDays(1)->format('Y-m-d');
            if(in_array($day, $result->weekly_progress)) {
                $stages += (int) $result->weekly_progress[$day];
            }
        } 

        return $stages;
    }

    public static function getGroups($date = null)
    { 
        $_groups = ProfileGroup::where('active', 1)->get();

        self::$courses = Course::get();

        $groups = [];

        foreach ($_groups as $key => $group) {
            $users = self::getUsers($group->id, $date);
            array_push($groups, self::getGroupItem($users, $group));
        }

        return [
            'items' => $groups,
            'fields' => self::getGroupFields()    
        ];
    }

    private static function getGroupItem($users, $group) {
        $points = 0;
        $progress = 0;
        foreach ($users['items'] as $key => $user) {
            $points += $user['points'];
            $progress += $user['progress_number'];
        }

        $arr = [];
        $arr['name'] = $group->name;
        $arr['group_id'] = $group->id;
        $arr['progress'] = $progress . '%';
        $arr['points'] = $points;
        return $arr;
    }

    private static function getGroupFields() {
        $arr = [];
        $arr[] = [
            'key' => 'name',
            'name' => 'Группа',
            'class' => 'text-left'
        ];
        $arr[] = [
            'key' => 'points',
            'name' => 'Заработано бонусов',
            'class' => 'text'
        ];
        $arr[] = [
            'key' => 'progress',
            'name' => 'Прогресс',
            'class' => 'text'
        ];
        
        return $arr;
    }


    /**
     * Get active course of user
     * 
     * $id for compare is the active course 
     * 
     * @return Course
     */
    public static function activeCourse($id = 0)
    {
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

        $courses = array_unique($courses);

        $results = self::where('user_id', $user_id)
            ->whereIn('status', [1])
            ->get()
            ->pluck('course_id')
            ->toArray();
        
        $results = array_unique($results);

        $diff = array_values(array_diff($courses, $results));
        
        $active_course = null;

        // if exists active course
        if(count($diff) > 0) {

            $course = Course::whereIn('id', $diff)
                ->orderBy('order')    
                ->first();

            $course_id = $course ? $course->id : 0;
          
            $active_course = self::where('user_id', $user_id)
                //->whereIn('status', [0,2])
                ->where('course_id', $course_id)
                ->orderBy('status', 'desc')
                ->first();
            
            if($active_course) {
              
                if($active_course->status == self::COMPLETED) {
                    $active_course = null;
                }
            } else {
        
                $active_course = self::create([
                    'user_id' => $user_id,
                    'course_id' => $course_id,
                    'status' => self::ACTIVE,
                    'progress' => 0, // 0 - 100
                    'points'=> 0, 
                    'started_at' => now(), 
                    'ended_at' => null, 
                ]);
            }
        }

        $course = null;

        // img poster
        if($active_course) {
            $course = Course::with('items')->find($active_course->course_id);

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

        if($course) {
            $course->is_active = $course->id == $id || $id == 0;
        } 

        return $course;
    }

    /**
    *   @return array
    *
    *  order => course_id
    *  [   
    *     1 => 2,
    *     2 => 3
    *  ]
    */
    public static function getCourseIds($user_id) {
        // prepare
        $user = User::withTrashed()->find($user_id);
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

        return Course::whereIn('id', array_unique($courses))
            ->orderBy('order')
            ->get()
            ->pluck('id', 'order')
            ->toArray();
    }

    public static function activeCourses() {
        
        $user_id = auth()->id();
        $courses = self::getCourseIds($user_id);
    
        $results = self::where('user_id', $user_id)
            ->whereIn('status', [1])
            ->get()
            ->pluck('course_id')
            ->toArray();
     
        $results = array_unique($results);

        $diff = array_values(array_diff($courses, $results));
        
        $active_courses = [];

        if(count($diff) > 0) {
            $active_courses = Course::whereIn('id', $diff)->orderBy('order', 'asc')->get();

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
            
            foreach ($active_courses as $key => $course) {

                $text = trim($course->text);
                if($text == '') $text = 'Нет описания';
                $text = strlen($text) >= 100 ? mb_substr($text, 0, 100) . '...' : $text;
                $course->text = $text;

                if($course->img != null && $disk->exists($course->img)) {
                    $course->img = $disk->temporaryUrl(
                        $course->img, now()->addMinutes(360)
                    );
                }
            }
        }

        if(is_array($active_courses)){
            return $active_courses;
        }
        else{
            return $active_courses->toArray();
        }
    }
}
