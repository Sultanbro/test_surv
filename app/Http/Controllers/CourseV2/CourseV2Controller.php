<?php

namespace App\Http\Controllers\CourseV2;

use App\Models\CourseV2;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Service\CourseV2\CourseV2Service;
use App\Http\Requests\CoursesV2\FilterCourseV2Request;
use App\Http\Requests\CoursesV2\CreateCourseV2Request;
use App\Http\Requests\CoursesV2\FilterAssignedCourseV2Request;

class CourseV2Controller extends Controller
{
    /**
     * Get all courses
     *
     * @param FilterCourseV2Request $request
     * @param CourseV2Service $service
     * @return JsonResponse
     */
    public function getAll(FilterCourseV2Request $request, CourseV2Service $service): JsonResponse
    {
        return $this->response(
            message: self::SUCCESS_MESSAGE,
            data: $service->getAll($request->toDto())
        );
    }

    /**
     * Create course
     *
     * @param CreateCourseV2Request $request
     * @param CourseV2Service $service
     * @return JsonResponse
     */
    public function create(CreateCourseV2Request $request, CourseV2Service $service): JsonResponse
    {
        return $this->response(
            message: self::SUCCESS_MESSAGE,
            data: $service->create($request->toDto())
        );
    }

    /**
     * Get one course
     *
     * @param $course
     * @param CourseV2Service $service
     * @return JsonResponse
     */
    public function getOne($course, CourseV2Service $service): JsonResponse
    {
        return $this->response(
            message: self::SUCCESS_MESSAGE,
            data: $service->getOne($course)
        );
    }

    /**
     * Get assigned courses
     *
     * @param FilterAssignedCourseV2Request $request
     * @param CourseV2Service $service
     * @return JsonResponse
     */
    public function getAssigned(FilterAssignedCourseV2Request $request, CourseV2Service $service): JsonResponse
    {
        return $this->response(
            message: self::SUCCESS_MESSAGE,
            data: $service->getAssigned($request->toDto())
        );
    }


    /**
     * Update course
     *
     * @param CreateCourseV2Request $request
     * @param CourseV2 $course
     * @param CourseV2Service $service
     * @return JsonResponse
     */
    public function update(CreateCourseV2Request $request, CourseV2 $course, CourseV2Service $service): JsonResponse
    {
        return $this->response(
            message: self::SUCCESS_MESSAGE,
            data: $service->update($course, $request->toDto())
        );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param CourseV2 $course
     * @param CourseV2Service $service
     * @return JsonResponse
     */
    public function destroy(CourseV2 $course, CourseV2Service $service): JsonResponse
    {
        return $this->response(
            message: self::SUCCESS_MESSAGE,
            data: $service->delete($course)
        );
    }
}
