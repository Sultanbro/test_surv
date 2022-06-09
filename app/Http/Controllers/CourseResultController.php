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
}
