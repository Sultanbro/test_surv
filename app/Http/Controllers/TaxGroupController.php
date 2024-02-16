<?php

namespace App\Http\Controllers;

use App\Http\Requests\Tax\SetTaxGroupAssigneeRequest;
use App\Http\Requests\Tax\TaxGroupRequest;
use App\Service\Tax\TaxesService;
use Illuminate\Http\JsonResponse;

class TaxGroupController extends Controller
{
    public function __construct(
        protected TaxesService $service
    )
    {
    }

    public function getAll(): JsonResponse
    {
        return $this->response(
            message: 'Success',
            data: $this->service->getAll()
        );
    }

    public function getUserTaxes(): JsonResponse
    {
        return $this->response(
            message: 'Success',
            data: $this->service->getUserTaxes(auth()->id())
        );
    }

    public function getOne($id): JsonResponse
    {
        return $this->response(
            message: 'Success',
            data: $this->service->getOne($id)
        );
    }

    public function create(TaxGroupRequest $request): JsonResponse
    {
        return $this->response(
            message: 'Success',
            data: $this->service->create($request->toDto())
        );
    }

    public function update(TaxGroupRequest $request, $id): JsonResponse
    {
        return $this->response(
            message: 'Success',
            data: $this->service->update($request->toDto(), $id)
        );
    }

    public function delete($id): JsonResponse
    {
        return $this->response(
            message: 'Success',
            data: $this->service->delete($id)
        );
    }

    public function setAssigned(SetTaxGroupAssigneeRequest $request): JsonResponse
    {
        return $this->response(
            message: 'Success',
            data: $this->service->setAssigned($request->toDto())
        );
    }
}
