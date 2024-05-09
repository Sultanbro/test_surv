<?php

namespace App\Http\Controllers\Tariff;

use App\Http\Controllers\Controller;
use App\Models\CentralUser;
use App\Models\Tariff\Tariff;
use App\Models\Tariff\TariffSubscription;
use App\Service\Payments\Core\GetTariffsService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

class TariffInformationController extends Controller
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
    public function tenantPaymentInfo(): JsonResponse
    {
        $tariff = TariffSubscription::getValidTariffPayment();
        return $this->response(
            message: 'success',
            data: $tariff,
        );
    }
}
