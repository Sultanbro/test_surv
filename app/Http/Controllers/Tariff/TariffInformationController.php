<?php

namespace App\Http\Controllers\Tariff;

use App\Http\Controllers\Controller;
use App\Models\Tariff\TariffSubscription;
use App\Service\Payment\Core\TariffListService;
use App\User;
use Exception;
use Illuminate\Http\JsonResponse;

class TariffInformationController extends Controller
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
    public function tenantPaymentInfo(): JsonResponse
    {
        $tariff = TariffSubscription::getValidTariffPayment();
        return $this->response(
            message: 'success',
            data: [
                'tariff' => $tariff,
                'users_count' => User::query()->count() - 1
            ]
        );
    }
}
