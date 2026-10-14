<?php

namespace App\Http\Controllers;

use App\Http\Requests\Admin\PaperRequest;
use App\Service\Admin\PaperService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PaperController extends Controller
{
    public function __construct(public PaperService $service)
    {
    }

    /**
     * Display a listing of the resource.
     *
     */
    public function getAll(): JsonResponse
    {
        return $this->response(
            message: "Success",
            data: $this->service->getAll()
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param PaperRequest $request
     * @return JsonResponse
     * @throws \Exception
     */
    public function store(PaperRequest $request): JsonResponse
    {
        return $this->response(
            message: "Success",
            data: $this->service->store($request->toDto())
        );
    }


    /**
     * Show the form for creating a new resource.
     *
     */
    public function getOne($id): JsonResponse
    {
        return $this->response(
            message: "Success",
            data: $this->service->getOne($id)
        );
    }


    /**
     * Update the specified resource in storage.
     *
     * @param PaperRequest $request
     * @param $id
     * @return JsonResponse
     * @throws \Exception
     */
    public function update(PaperRequest $request, $id): JsonResponse
    {
        return $this->response(
            message: "Success",
            data: $this->service->update($id, $request->toDto())
        );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param $id
     * @return JsonResponse
     */
    public function delete($id): JsonResponse
    {
        return $this->response(
            message: "Success",
            data: $this->service->delete($id)
        );
    }

    /**
     * Search the specified resources from storage.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function search(Request $request): JsonResponse
    {
        return $this->response(
            message: "Success",
            data: $this->service->search($request->get('query'))
        );
    }
}
