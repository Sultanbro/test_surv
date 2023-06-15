<?php

namespace App\Http\Controllers\Top;

use App\Http\Controllers\Controller;
use App\Http\Requests\Top\ArchiveUtilityRequest;
use App\Http\Requests\Top\SwitchRequest;
use App\Http\Resources\Top\SwitchListResource;
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
            data: SwitchListResource::collection(ProfileGroup::getActiveProfileGroupsAnyAnalytics(ProfileGroup::SWITCH_UTILITY))
        );
    }
    /**
     * Получаем список активных группы с аналитикой 1 и -1
     * @return JsonResponse
     */
    public function listRentability(): JsonResponse {
        return $this->response(
            message: 'Success',
            data: SwitchListResource::collection(ProfileGroup::getActiveProfileGroupsAnyAnalytics(ProfileGroup::SWITCH_RENTABILITY))
        );
    }

    /**
     * Получаем список активных группы с аналитикой 1 и -1
     * @return JsonResponse
     */
    public function listProceeds(): JsonResponse {
        return $this->response(
            message: 'Success',
            data: SwitchListResource::collection(ProfileGroup::getActiveProfileGroupsAnyAnalytics(ProfileGroup::SWITCH_PROCEEDS))
        );
    }


    public function switch(SwitchRequest $request): JsonResponse {

        $response = ProfileGroup::updateSwitch($request->toDto());

        return $this->response(
            message: 'Success'
        );
    }
}
