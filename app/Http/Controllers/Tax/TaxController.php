<?php

namespace App\Http\Controllers\Tax;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tax\CreateTaxRequest;
use App\Http\Requests\Tax\GetTaxesRequest;
use App\Http\Requests\Tax\TaxSetAssigneeRequest;
use App\Http\Requests\Tax\UpdateTaxRequest;
use App\Service\Tax\CreateTaxService;
use App\Service\Tax\DeleteTaxService;
use App\Service\Tax\GetTaxesService;
use App\Service\Tax\SetAssigneeService;
use App\Service\Tax\UpdateTaxService;
use Illuminate\Http\JsonResponse;
use App\Models\Tax;
use Exception;

class TaxController extends Controller
{
    /**
     * @param GetTaxesRequest $request
     * @param GetTaxesService $service
     * @return JsonResponse
     */
    public function get(GetTaxesRequest $request, GetTaxesService $service): JsonResponse
    {
        return $this->response(
            message: "Success",
            data: $service->handle($request->toDto()->userId)
        );
    }

    /**
     * @return JsonResponse
     */
    public function all(): JsonResponse
    {
        return $this->response(
            message: 'Success',
            data: Tax::all()
        );
    }

    /**
     * @param CreateTaxRequest $request
     * @param CreateTaxService $service
     * @return JsonResponse
     * @throws Exception
     */
    public function create(CreateTaxRequest $request, CreateTaxService $service): JsonResponse
    {
        return $this->response(
            message: 'Success',
            data: $service->handle($request->toDto())
        );
    }

    /**
     * @param UpdateTaxRequest $request
     * @param UpdateTaxService $service
     * @return JsonResponse
     * @throws Exception
     */
    public function update(UpdateTaxRequest $request, UpdateTaxService $service): JsonResponse
    {
        return $this->response(
            message: 'Success',
            data: $service->handle($request->toDto())
        );
    }

    /**
     * @param TaxSetAssigneeRequest $request
     * @param SetAssigneeService $service
     * @return JsonResponse
     * @throws Exception
     */
    public function setAssigned(TaxSetAssigneeRequest $request, SetAssigneeService $service): JsonResponse
    {
        return $this->response(
            message: 'Success',
            data: $service->handle($request->toDto())
        );
    }

    /**
     * @param int $id
     * @param DeleteTaxService $service
     * @return JsonResponse
     * @throws Exception
     */
    public function delete(int $id, DeleteTaxService $service): JsonResponse
    {
        return $this->response(
            message: 'Success',
            data: $service->handle($id)
        );
    }
}
