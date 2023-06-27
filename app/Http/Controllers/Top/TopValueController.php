<?php

namespace App\Http\Controllers\Top;

use App\Http\Controllers\Controller;
use App\Http\Requests\Top\ArchiveUtilityRequest;
use App\Http\Requests\Top\SwitchRequest;
use App\Http\Resources\Top\ProceedSwitchResource;
use App\Http\Resources\Top\RentabilitySwitchResource;
use App\Http\Resources\Top\UtilitySwitchResource;
use App\ProfileGroup;
use App\Service\Top\ArchiveUtilityForGroupService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TopValueController extends Controller
{
    /**
     * @param ArchiveUtilityRequest $request
     * @param ArchiveUtilityForGroupService $service
     * @return JsonResponse
     */
    public function archiveUtility(ArchiveUtilityRequest $request, ArchiveUtilityForGroupService $service): JsonResponse
    {
        $response = $service->handle($request->toDto());

        return $this->response(
            message: 'Success',
            data: $response
        );
    }

    /**
     * Получаем список активных группы с аналитикой 1 и -1
     * @return JsonResponse
     */
    public function listUtility(): JsonResponse {
        return $this->response(
            message: 'Success',
            data: UtilitySwitchResource::collection(ProfileGroup::getActiveProfileGroupsAnyAnalytics())
        );
    }
    /**
     * Получаем список активных группы с аналитикой 1 и -1
     * @return JsonResponse
     */
    public function listRentability(): JsonResponse {
        return $this->response(
            message: 'Success',
            data: RentabilitySwitchResource::collection(ProfileGroup::getActiveProfileGroupsAnyAnalytics())
        );
    }

    /**
     * Получаем список активных группы с аналитикой 1 и -1
     * @return JsonResponse
     */
    public function listProceeds(): JsonResponse {
        return $this->response(
            message: 'Success',
            data: ProceedSwitchResource::collection(ProfileGroup::getActiveProfileGroupsAnyAnalytics())
        );
    }


    public function switch(SwitchRequest $request): JsonResponse {

        $response = ProfileGroup::updateSwitch($request->toDto());

        return $this->response(
            message: 'Success'
        );
    }
}
