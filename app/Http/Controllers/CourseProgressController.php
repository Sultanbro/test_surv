<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProgressCourseRequest;
use App\Service\Courses\CourseProgressService;
use Illuminate\Http\Request;

class CourseProgressController extends Controller
{
    public function __invoke(ProgressCourseRequest $request)
    {
        $service = new CourseProgressService($request->toDto());
        $response = $service->handle();

        return response()->success($response);
    }
}
