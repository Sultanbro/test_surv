<?php

namespace App\Http\Controllers\Subscription;

use App\Facade\Payment\Gateway;
use App\Http\Controllers\Controller;
use App\Http\Requests\Subscription\CreateSubscriptionRequest;
use App\Models\Tariff\TariffSubscription;
use App\Service\Payment\Core\PaymentUpdateStatusService;
use App\Service\Subscription\Pipeline\SubscriptionPipeline;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ExtendSubscriptionController extends Controller
{

    public function __invoke(CreateSubscriptionRequest $request, TariffSubscription $subscription): JsonResponse
    {
        $pipeline = new SubscriptionPipeline($request->toDto());
        $pipeline->apply();

        return $this->response(
            message: 'Success',
            data: $pipeline->invoice()
        );
    }
}
