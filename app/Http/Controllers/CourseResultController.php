<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use Carbon\Carbon;
use App\User;
use App\ProfileGroup;
use App\Models\Course;
use App\Models\CourseResult;

class CourseResultController extends Controller
{
    public function get(Request $request)
    {   
        $date = Carbon::createFromDate($request->year, $request->month, 1)->format('Y-m-d');

        if($request->type == 'users') {
            $items = CourseResult::getUsers($request->group_id, $date);
        }

        if($request->type == 'groups') {
            $items = CourseResult::getGroups($date);
        }

        return [
            'items' => $items,
        ];
    } 

    /**
     * Nullify progress in course for user
     * 
     * @param Request $request
     */
    public function nullify(Request $request)
    {   
        CourseResult::query()
                    ->where('user_id', $request->user_id)
                    ->where('course_id', $request->course_id)
                    ->update([
                        'progress'        => 0,
                        'status'          => CourseResult::INITIAL,
                        'points'          => 0,
                        'started_at'      => null,
                        'ended_at'        => null,
                        'weekly_progress' => null,
                    ]);
    } 

}
