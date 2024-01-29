<?php

namespace App\Http\Controllers;

use App\Models\CentralCourse;
use App\Service\CourseV2\CentralCourseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CentralCourseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @param CentralCourseService $service
     * @return JsonResponse
     */
    public function getAll(Request $request, CentralCourseService $service): JsonResponse
    {
        return $this->response(
            message: self::SUCCESS_MESSAGE,
            data: $service->getAll()
        );
    }
}
