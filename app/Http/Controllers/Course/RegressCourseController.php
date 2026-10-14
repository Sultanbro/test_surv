<?php

namespace App\Http\Controllers\Course;

use App\Http\Controllers\Controller;
use App\Http\Requests\CourseRegressRequest;
use App\Service\Courses\CourseRegressor;
use Exception;
use Illuminate\Http\Request;

class RegressCourseController extends Controller
{
    /**
     * @param CourseRegressRequest $request
     * @return mixed
     * @throws Exception
     */
    public function regress(CourseRegressRequest $request)
    {
        $type = $request->input('type');
        $data = $request->all();
        $courseRegress = app(CourseRegressor::class);
        $response = $courseRegress->handle($type)->regress($data);

        return response()->success($response);
    }
}
