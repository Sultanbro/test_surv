<?php

namespace App\Http\Controllers;

use App\Http\Requests\Admin\FaqRequest;
use App\Http\Requests\Admin\SetOrderFaqRequest;
use App\Service\Admin\FaqService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Exception;

class FaqController extends Controller
{
    public function __construct(public FaqService $service)
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
            data: $this->service->getTree()
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param FaqRequest $request
     * @return JsonResponse
     * @throws Exception
     */
    public function store(FaqRequest $request): JsonResponse
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
    public function setOrder(SetOrderFaqRequest $request): JsonResponse
    {
        return $this->response(
            message: "Success",
            data: $this->service->setOrder($request->get('items'))
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
     * @param FaqRequest $request
     * @param $id
     * @return JsonResponse
     * @throws Exception
     */
    public function update(FaqRequest $request, $id): JsonResponse
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
