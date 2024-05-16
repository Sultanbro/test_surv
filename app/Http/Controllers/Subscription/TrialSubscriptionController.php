<?php

namespace App\Http\Controllers\Subscription;

use App\DTO\Payment\CreateInvoiceDTO;
use App\Http\Controllers\Controller;
use App\Models\Tariff\PaymentToken;
use App\Models\Tariff\Tariff;
use App\Service\Subscription\SubscribeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TrialSubscriptionController extends Controller
{

    public function enable(Request $request): JsonResponse
    {
        $tenant = $request->get('tenant_id') ?? tenant('id');
        $tariff = Tariff::query()->find();
        $service = new SubscribeService(new CreateInvoiceDTO(

        ), new PaymentToken("trial"));
        return $this->response(
            message: 'Success',
            data: []
        );
    }
}
