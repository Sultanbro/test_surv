<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use App\Service\Payments\Core\TariffGetAllService;
use Exception;
use Illuminate\Http\JsonResponse;

class TariffController extends Controller
{
    /**
     * @param TariffGetAllService $tariffGetAllService
     */
    public function __construct(
        public TariffGetAllService $tariffGetAllService
    )
    {}

    /**
     * @return JsonResponse
     * @throws Exception
     */
    public function get(): JsonResponse
    {
        return $this->response(
            message: 'success',
            data: $this->tariffGetAllService->handle(),
        );
    }
}
