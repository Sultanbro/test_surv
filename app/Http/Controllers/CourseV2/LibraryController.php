<?php

namespace App\Http\Controllers\CourseV2;

use App\Http\Controllers\Controller;
use App\Http\Resources\CoursesV2\LibraryCourseResource;
use App\Models\CentralCourse;
use App\Service\CourseV2\LibraryService;
use Illuminate\Http\Request;

class LibraryController extends Controller
{
    public function getCategories(Request $request, LibraryService $service)
    {
        return $this->response(
            message: self::SUCCESS_MESSAGE,
            data: $service->categories()
        );
    }

    public function getLatestCourses(Request $request, LibraryService $service)
    {
        return $this->response(
            message: self::SUCCESS_MESSAGE,
            data: $service->latestCourses()
        );
    }

    public function getCourse(Request $request, CentralCourse $course)
    {
        return $this->response(
            message: self::SUCCESS_MESSAGE,
            data: new LibraryCourseResource($course)
        );
    }
}
