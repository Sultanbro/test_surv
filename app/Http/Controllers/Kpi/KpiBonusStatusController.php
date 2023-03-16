<?php

namespace App\Http\Controllers\Kpi;

use App\Http\Controllers\Controller;
use App\Http\Requests\Kpi\KpiBonusStatusRequest;
use App\Models\Kpi\Bonus;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class KpiBonusStatusController extends Controller
{
    /**
     * @param KpiBonusStatusRequest $request
     * @return JsonResponse
     * @throws Exception
     */
    public function setActive(KpiBonusStatusRequest $request): JsonResponse
    {
        $dto = $request->toDto();
        return $this->response(
            message: 'Success',
            data: Bonus::setActive($dto->bonusId, $dto->isActive)
        );
    }
}
