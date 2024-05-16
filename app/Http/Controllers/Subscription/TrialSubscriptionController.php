<?php

namespace App\Http\Controllers\Subscription;

use App\DTO\Payment\CreateInvoiceDTO;
use App\Enums\Tariff\TariffKindEnum;
use App\Facade\Payment\Gateway;
use App\Http\Controllers\Controller;
use App\Models\Tariff\PaymentToken;
use App\Models\Tariff\Tariff;
use App\Models\Tariff\TariffSubscription;
use App\Service\Subscription\SubscribeService;
use App\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TrialSubscriptionController extends Controller
{

    public function enable(Request $request): JsonResponse
    {
        $tenant = $request->get('tenant_id') ?? tenant('id');
        $tariff = Tariff::pro();
        /** @var User $user */
        $user = $request->user();
        $invoice = new CreateInvoiceDTO(
            $user->currency,
            $tariff->id,
            $tenant,
            0,
            Gateway::provider($user->currency)->name(),
            now()->addMonth()->toDateTimeString()
        );

        $service = new SubscribeService($invoice, new PaymentToken("trial"));
        $service->subscribe();

        return $this->response(
            message: 'Success',
            data: []
        );
    }

    public function exists(Request $request): JsonResponse
    {
        $tenant = $request->get('tenant_id') ?? tenant('id');

        return $this->response(
            message: 'Success',
            data: [
                'has_trial' => TariffSubscription::hasTrial($tenant),
            ]
        );
    }
}
