<?php

namespace App\Http\Controllers\Subscription;

use App\DTO\Payment\NewSubscriptionDTO;
use App\Enums\Payments\PaymentStatusEnum;
use App\Enums\Tariff\TariffKindEnum;
use App\Facade\Payment\Gateway;
use App\Http\Controllers\Controller;
use App\Models\Tariff\Transaction;
use App\Models\Tariff\Tariff;
use App\Models\Tariff\TariffSubscription;
use App\Service\Subscription\SubscribeService;
use App\User;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TrialSubscriptionController extends Controller
{

    /**
     * @throws Exception
     */
    public function enable(Request $request): JsonResponse
    {
        $tenant = $request->get('tenant_id') ?? tenant('id');
        $tariff = Tariff::pro();
        /** @var User $user */
        $user = $request->user();
        $invoice = new NewSubscriptionDTO(
            currency: $user->currency,
            tariffId: $tariff->id,
            tenantId: $tenant,
            extraUsersLimit: 0,
            provider: Gateway::provider($user->currency)->name(),
            expiate_at: now()->addMonth()->toDateTimeString()
        );

        $service = new SubscribeService($invoice, new Transaction("trial"));

        $subscription = $service->subscribe();
        $subscription->status = PaymentStatusEnum::STATUS_SUCCESS;
        $subscription->save();

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
                'has_trial' => TariffSubscription::hasValidTariffPayment() ?? TariffSubscription::hasTrial($tenant),
            ]
        );
    }
}
