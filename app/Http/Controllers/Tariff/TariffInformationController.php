<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use App\Models\CentralUser;
use App\Models\Tariff\Tariff;
use App\Models\Tariff\TariffPayment;
use App\Service\Payments\Core\TariffGetAllService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

class TariffInformationController extends Controller
{
    /**
     * @param TariffGetAllService $tariffGetAllService
     */
    public function __construct(
        public TariffGetAllService $tariffGetAllService
    )
    {
    }

    /**
     * @return JsonResponse
     * @throws Exception
     */
    public function get(): JsonResponse
    {
        $owner = CentralUser::fromAuthUser();
        $tariff = TariffPayment::getValidTariffPayment();
        return $this->response(
            message: 'success',
            data: $tariff,
        );
    }
}
