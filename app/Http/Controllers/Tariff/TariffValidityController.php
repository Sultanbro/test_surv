<?php

namespace App\Http\Controllers\Tariff;

use App\Http\Controllers\Controller;
use App\Models\Tariff\TariffSubscription;
use Illuminate\Http\JsonResponse;

class TariffValidityController extends Controller
{
    public function validity(): JsonResponse
    {
        $tariff = TariffSubscription::getValidTariffPayment();
        return $this->response(
            message: 'success',
            data: [
                'expired_at' => $tariff?->expire_date
            ]
        );
    }
}
