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
use App\Models\TestQuestion;

class CourseResult extends Model
{
    protected $table = 'course_results';

    public $timestamps = true;

    protected $fillable = [
        'user_id',
        'course_id',
        'status',
        'progress', // 0 - 100
        'points', 
        'started_at', 
        'ended_at', 
    ];

    // status
    CONST INITIAL = 0;
    CONST COMPLETED = 1;
    CONST STARTED = 2;


    public static $courses;

    public static function getUsers($group_id, $date = null)
    {
        $user_ids = ProfileGroup::employees($group_id);
        $users = [];

        self::$courses = Course::get();

        foreach ($user_ids as $key => $user_id) {
            $user = User::withTrashed()
                ->with('course_results')
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
            'name' => 'Набрано баллов',
            'class' => 'text'
        ];
        $arr[] = [
            'key' => 'progress',
            'name' => 'Прогресс',
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

        $arr['name'] = $user->LAST_NAME . ' ' . $user->NAME;
        $arr['user_id'] = $user->ID;
        $arr['status'] = $uc['totals']['status'] == 2 ? 'Начат' : 'Завершен';
        $arr['progress'] = $uc['totals']['progress'] . '%' ;
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
        $progress_count = 0;

        $first_date = self::$courses->sort(function ($a, $b) {
            return strtotime($a->started_at) < strtotime($b->started_at);
        })->first();

        $last_date = self::$courses->sort(function ($a, $b) {
            return strtotime($a->ended_at) < strtotime($b->ended_at);
        })->first();

        $status = self::$courses->where('status', 2)->first() ? 2 : 1;

        foreach($user->course_results as $result) {

            $course = self::$courses->where('id', $result->id)->first();
            if($course) {
                $arr = [];
                $arr['name'] = $course->name;
                $arr['status'] = $course->status == 2 ? 'Начат' : 'Завершен';
                $arr['user_id'] = $user->ID;

                $progress += $course->progress;
                $progress_count++;
                $arr['progress'] = $course->progress;
                
                $points += $course->points;
                $arr['points'] = $course->points;

                
                $arr['started_at'] = $course->started_at ? Carbon::parse($course->started_at)->format('d.m.Y') : '';
                $arr['ended_at'] =  $course->ended_at ? Carbon::parse($course->ended_at)->format('d.m.Y') : '';
                
                array_push($arrx, $arr);
            }
        }

        return [
            'courses' => $arrx,
            'totals' => [
                'points' => $points,
                'progress' => $progress_count > 0 ? round($progress / $progress_count) : 0,
                'status' => $status,
                'started_at' => $first_date,
                'ended_at' => $last_date,
            ]
        ];
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
            $progress += $user['progress'];
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
            'name' => 'Набрано баллов',
            'class' => 'text'
        ];
        $arr[] = [
            'key' => 'progress',
            'name' => 'Прогресс',
            'class' => 'text'
        ];
        
        return $arr;
    }
}
