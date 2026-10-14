<?php

namespace App\Http\Controllers\Course;

use App\Http\Requests\Courses\GetCourseItemRequest;
use App\Http\Requests\Courses\GetCourseItemResultRequest;
use App\Repositories\CourseItemRepository;
use App\Repositories\CourseResultRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Http\Controllers\Controller;
use App\Models\CourseResult;
use App\Service\Courses\CourseResultService;
use Illuminate\Support\Facades\Auth;

class CourseResultController extends Controller
{   
    public $results;

    public function __construct(CourseResultService $crs)
    {
        $this->results = $crs;
    }

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

    public function nullify(Request $request)
    {   
        $this->results->nullify($request->course_id, $request->user_id);
    }

    /**
     * @param GetCourseItemResultRequest $request
     * @return JsonResponse
     */
    public function getCourseItemAndResult(GetCourseItemResultRequest $request): JsonResponse
    {
        $courseIds = $request->toDto()->ids;
        $userId = Auth::id();

        return $this->response(
            message: 'Success',
            data: [
                'course_results'    => (new CourseResultRepository)->getResultByCourseIds($courseIds, $userId)->get(),
                'course_items'      => (new CourseItemRepository)->getItemByCourseIds($courseIds)->get()
            ]
        );
    }
}
