<?php

namespace App\Http\Controllers\Course;

use App\Http\Requests\ProgressCourseRequest;
use App\Service\Courses\CourseProgressService;
use App\Http\Controllers\Controller;

class CourseProgressController extends Controller
{
    public function progress(ProgressCourseRequest $request)
    {
        $service = new CourseProgressService($request->toDto());
        $response = $service->handle();

        return response()->success($response);
    }
}
