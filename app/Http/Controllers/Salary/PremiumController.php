<?php

namespace App\Http\Controllers\Salary;

use App\Http\Controllers\Controller;
use App\Http\Requests\Premium\EditPremiumRequest;
use App\Service\Premium\PremiumEditService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PremiumController extends Controller
{
    /**
     * @param EditPremiumRequest $request
     * @param PremiumEditService $service
     * @return JsonResponse
     */
    public function edit(EditPremiumRequest $request, PremiumEditService $service)
    {
        $dto = $request->toDto();
        $response = $service->edit(
            $dto->type,
            $dto->userId,
            $dto->amount,
            $dto->comment,
            $dto->date
        );

        return $this->response(
            message: 'Successfully edited',
            data: $response
        );
    }
}
