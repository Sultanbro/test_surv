<?php

namespace App\Http\Controllers\Tariff;

use App\Http\Controllers\Controller;
use App\Service\Payments\Core\GetTariffsService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

class TariffController extends Controller
{
    /**
     * @param GetTariffsService $tariffGetAllService
     */
    public function __construct(
        public GetTariffsService $tariffGetAllService
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
            data: Cache::driver('central')
                ->rememberForever('currencies', fn() => $this->tariffGetAllService->handle()),
        );
    }
}
