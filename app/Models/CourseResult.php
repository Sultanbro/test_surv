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
        ];
        $arr[] = [
            'key' => 'progress',
            'name' => 'Прогресс',
        ];
        $arr[] = [
            'key' => 'points',
            'name' => 'Набрано баллов',
        ];
        return $arr;
    }
    
    private static function getUserItem($user, $date) {
        $arr = [];
        $arr['name'] = $user->LAST_NAME . ' ' . $user->NAME;
        $arr['user_id'] = $user->ID;
        $arr['progress'] = 25;
        //$arr['assigned_at'] = date('Y-m-d');
        $arr['points'] = 2150;
        return $arr;
    }

    public static function getGroups($date = null)
    {
        return [];
    }
}
