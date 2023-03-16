<?php

namespace App\Http\Controllers\Kpi;

use App\Http\Controllers\Controller;
use App\Http\Requests\Kpi\QuartalPremiumStatusRequest;
use App\Models\QuartalPremium;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class QuartalPremiumStatusController extends Controller
{
    /**
     * @param QuartalPremiumStatusRequest $request
     * @return JsonResponse
     * @throws Exception
     */
    public function setActive(QuartalPremiumStatusRequest $request): JsonResponse
    {
        $dto = $request->toDto();
        return $this->response(
            message: 'Success',
            data: QuartalPremium::setActive($dto->premiumId, $dto->isActive)
        );
    }
}
