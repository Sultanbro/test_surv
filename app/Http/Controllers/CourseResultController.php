<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\User;
use App\ProfileGroup;
use App\Models\CourseResult;
use App\Service\Courses\CourseResultService;

class CourseResultController extends Controller
{   
    /**
     * Service
     */
    public $results;

    public function __construct(CourseResultService $crs)
    {
        $this->results = $crs;
    }

    /**
     * get results
     */
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
        $this->results->nullify($request->course_id, $request->user_id);
    } 

}
