<?php

namespace App\Http\Controllers\Tariff;

use App\Http\Controllers\Controller;
use App\Service\Tariff\TariffListService;
use Exception;
use Illuminate\Http\JsonResponse;

class TariffController extends Controller
{
    /**
     * @param TariffListService $tariffGetAllService
     */
    public function __construct(
        public TariffListService $tariffGetAllService
    )
    {
    }

    /**
     * @return JsonResponse
     * @throws Exception
     */
    public function get(): JsonResponse
    {
        return $this->response(
            message: 'success',
            data:  $this->tariffGetAllService->handle(),
        );
    }
}
