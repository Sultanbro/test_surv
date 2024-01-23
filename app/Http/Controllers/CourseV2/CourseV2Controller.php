<?php

namespace App\Http\Controllers\CourseV2;

use App\Http\Controllers\Controller;
use App\Http\Requests\CoursesV2\CreateCourseV2Request;
use App\Http\Requests\CoursesV2\FilterCourseV2Request;
use App\Models\CourseV2;
use App\Service\CourseV2\CourseV2Service;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CourseV2Controller extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(FilterCourseV2Request $request, CourseV2Service $service)
    {
        return $this->response(
            message: self::SUCCESS_MESSAGE,
            data: $service->filter($request->toDto())
        );
    }

    /**
     * Show the form for creating a new resource.
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
     * Display the specified resource.
     *
     * @param  \App\Models\CourseV2  $courseV2
     * @return \Illuminate\Http\Response
     */
    public function show(CourseV2 $courseV2)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\CourseV2  $courseV2
     * @return \Illuminate\Http\Response
     */
    public function edit(CourseV2 $courseV2)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\CourseV2  $courseV2
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, CourseV2 $courseV2)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\CourseV2  $courseV2
     * @return \Illuminate\Http\Response
     */
    public function destroy(CourseV2 $courseV2)
    {
        //
    }
}
