<?php

namespace App\Http\Controllers\Kpi;

use App\Http\Controllers\Controller;
use App\Http\Requests\QuartalPremiumSaveRequest;
use App\Http\Requests\QuartalPremiumUpdateRequest;
use App\Service\QuartalPremiumService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class QuartalPremiumController extends Controller
{
    /**
     * Получить Квартальную премию.
     *
     * @param Request $request
     * @param QuartalPremiumService $service
     * @return JsonResponse
     */
    public function get(Request $request, QuartalPremiumService $service): JsonResponse
    {
        $response = $service->fetch($request->all());

        return response()->json($response);
    }

    /**
     * @param QuartalPremiumSaveRequest $request
     * @param QuartalPremiumService $service
     * @return JsonResponse
     */
    public function save(QuartalPremiumSaveRequest $request, QuartalPremiumService $service): JsonResponse
    {
        $response = $service->save($request);

        return response()->json($response);
    }

    /**
     * @param QuartalPremiumUpdateRequest $request
     * @param QuartalPremiumService $service
     * @return JsonResponse
     */
    public function update(QuartalPremiumUpdateRequest $request, QuartalPremiumService $service): JsonResponse
    {
        $dto = $request->toDto();

        $response = $service->update($dto);

        return response()->json($response);
    }

    /**
     * @param Request $request
     * @param QuartalPremiumService $service
     * @return JsonResponse
     */
    public function destroy(Request $request, QuartalPremiumService $service): JsonResponse
    {
        $response = $service->delete($request);

        return response()->json($response);
    }
}
