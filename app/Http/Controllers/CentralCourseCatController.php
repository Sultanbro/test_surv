<?php

namespace App\Http\Controllers;

use App\Http\Requests\CoursesV2\CentralCourseCatRequest;
use App\Service\CourseV2\CentralCourseCatService;
use Illuminate\Http\JsonResponse;
use App\Models\CentralCourseCat;

class CentralCourseCatController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param CentralCourseCatService $service
     * @return JsonResponse
     */
    public function getAll(CentralCourseCatService $service): JsonResponse
    {
        return $this->response(
            message: self::SUCCESS_MESSAGE,
            data: $service->getAll()
        );
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param CentralCourseCatRequest $request
     * @param CentralCourseCatService $service
     * @return JsonResponse
     */
    public function create(CentralCourseCatRequest $request, CentralCourseCatService $service): JsonResponse
    {
        return $this->response(
            message: self::SUCCESS_MESSAGE,
            data: $service->create($request->validated())
        );
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param CentralCourseCat $category
     * @return JsonResponse
     */
    public function getOne(CentralCourseCat $category): JsonResponse
    {
        return $this->response(
            message: self::SUCCESS_MESSAGE,
            data: $category
        );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param CentralCourseCatRequest $request
     * @param CentralCourseCat $category
     * @param CentralCourseCatService $service
     * @return JsonResponse
     */
    public function update(CentralCourseCatRequest $request, CentralCourseCat $category, CentralCourseCatService $service): JsonResponse
    {
        return $this->response(
            message: self::SUCCESS_MESSAGE,
            data: $service->update($category, $request->validated())
        );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param CentralCourseCat $category
     * @param CentralCourseCatService $service
     * @return JsonResponse
     */
    public function destroy(CentralCourseCat $category, CentralCourseCatService $service): JsonResponse
    {
        $service->delete($category);

        return $this->response(
            message: self::SUCCESS_MESSAGE
        );
    }
}
