<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Auth;
use Carbon\Carbon;
use App\User;
use App\ProfileGroup;
use App\Models\Course;
use App\Models\CourseResult;

class CourseResult extends Model
{
    protected $table = 'course_results';

    public $timestamps = true;

    protected $fillable = [
        'user_id',
        'course_id',
        'status',
        'progress', // 0 - 100
    ];

    // status
    CONST INITIAL = 0;
    CONST COMPLETED = 1;
    CONST STARTED = 2;


    public static function getUsers($group_id, $date = null)
    {
        $user_ids = ProfileGroup::employees($group_id);
        $users = [];

        foreach ($user_ids as $key => $user_id) {
            $user = User::withTrashed()->find($user_id);
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
        $arr['name'] = $user->LAST_NAME . ' ' . $user->NAME;
        $arr['user_id'] = $user->ID;
        $arr['status'] = rand(0,1) ? 'Начат' : 'Завершен';
        $arr['progress'] = rand(0,100) . '%' ;
        $arr['progress_number'] = rand(0,100);  
        $arr['points'] = rand(100,10000);
        $arr['expanded'] = false;
        $arr['started_at'] = Carbon::now()->subMonths(rand(0,3))->addDays(rand(0,10))->format('d.m.Y');
        $arr['ended_at'] = Carbon::now()->subMonths(rand(0,3))->addDays(rand(0,10))->format('d.m.Y');
        $arr['courses'] = self::getUserCourses($user->ID);
        return $arr;
    }

    private static function getUserCourses($user_id) {
        $arrx = [];

        $array1= array('Курс для UCALS','Презентация проекта','Переговоры в продажах','Расчет OS', 'AGILE проекты');

        
        for($i=0;$i<rand(1,5);$i++) {
            $arr = [];

            $a = rand(0,1);
            $arr['name'] = $array1[array_rand($array1, 1)];
            $arr['status'] = rand(0,1) ? 'Начат' : 'Завершен';
            $arr['user_id'] = $user_id;
            $arr['progress'] = rand(0,100);
           
            $arr['points'] = rand(0,1000);

            $arr['started_at'] = Carbon::now()->subMonths(rand(0,3))->addDays(rand(0,10))->format('d.m.Y');
            $arr['ended_at'] = Carbon::now()->subMonths(rand(0,3))->addDays(rand(0,10))->format('d.m.Y');
            array_push($arrx, $arr);
        }
        

        return $arrx;
    }

    private static function getGroupItem($users, $group) {

        $points = 0;
        $progress = 0;
        foreach ($users['items'] as $key => $user) {
            $points += $user['points'];
        }

        $arr = [];
        $arr['name'] = $group->name;
        $arr['group_id'] = $group->id;
        $arr['status'] = rand(0,1) ? 'Начат' : 'Завершен';
        $arr['progress'] = rand(0,100) . '%';
        $arr['points'] = $points;
        $arr['started_at'] = Carbon::now()->subMonths(rand(0,3))->addDays(rand(0,10))->format('d.m.Y');
        $arr['ended_at'] = Carbon::now()->subMonths(rand(0,3))->addDays(rand(0,10))->format('d.m.Y');
        return $arr;
    }

    public static function getGroups($date = null)
    { 
        $_groups = ProfileGroup::where('active', 1)->get();

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

    private static function getGroupFields() {
        $arr = [];
        $arr[] = [
            'key' => 'name',
            'name' => 'Группа',
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
}
