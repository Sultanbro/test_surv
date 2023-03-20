<?php

namespace App\Http\Controllers\Tax;

use App\DTO\Tax\GetTaxesResponseDTO;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Tax\Response\TaxGetResponse;
use App\Http\Requests\Tax\AddTaxRequest;
use App\Http\Requests\Tax\GetTaxesRequest;
use App\Http\Requests\Tax\TaxSetAssigneeRequest;
use App\Http\Requests\Tax\UpdateTaxRequest;
use App\Models\Tax;
use App\Service\Tax\CreateTax;
use App\Service\Tax\GetTaxes;
use App\Service\Tax\SetAssignee;
use App\Service\Tax\UpdateTax;
use Exception;
use Illuminate\Http\JsonResponse;

class TaxController extends Controller
{
    /**
     * @param GetTaxesRequest $request
     * @param GetTaxes $service
     * @return TaxGetResponse
     */
    public function get(GetTaxesRequest $request, GetTaxes $service): TaxGetResponse
    {
        $response = GetTaxesResponseDTO::fromArray($service->handle($request->toDto()->userId));

        return TaxGetResponse::success($response);
    }

    /**
     * @param AddTaxRequest $request
     * @param CreateTax $service
     * @return JsonResponse
     * @throws Exception
     */
    public function create(AddTaxRequest $request, CreateTax $service): JsonResponse
    {
        return $this->response(
            message: 'Success',
            data: $service->handle($request->toDto())
        );
    }

    /**
     * @param UpdateTaxRequest $request
     * @param UpdateTax $service
     * @return JsonResponse
     * @throws Exception
     */
    public function update(UpdateTaxRequest $request, UpdateTax $service): JsonResponse
    {
        return $this->response(
            message: 'Success',
            data: $service->handle($request->toDto())
        );
    }

    /**
     * @param TaxSetAssigneeRequest $request
     * @param SetAssignee $service
     * @return JsonResponse
     * @throws Exception
     */
    public function setAssigned(TaxSetAssigneeRequest $request, SetAssignee $service): JsonResponse
    {
        return $this->response(
            message: 'Success',
            data: $service->handle($request->toDto())
        );
    }
}
