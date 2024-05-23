<?php

namespace App\Http\Controllers\Subscription;

use App\DTO\Payment\CreateInvoiceDTO;
use App\Facade\Payment\Gateway;
use App\Http\Controllers\Controller;
use App\Http\Requests\Subscription\UpdateSubscriptionRequest;
use App\Models\CentralUser;
use App\Models\Tariff\TariffSubscription;
use App\Service\Payment\Core\CanCalculateTariffPrice;
use App\Service\Payment\Core\TariffListService;
use Illuminate\Http\JsonResponse;

class ExtendSubscriptionController extends Controller
{
    use CanCalculateTariffPrice;

    /**
     * @param TariffListService $tariffGetAllService
     */
    public function __construct(
        public TariffListService $tariffGetAllService
    )
    {
    }

    public function __invoke(UpdateSubscriptionRequest $request, TariffSubscription $subscription): JsonResponse
    {
        $data = $request->toDto();
        $customer = CentralUser::fromAuthUser()->customer();
        $gateway = Gateway::provider($data->provider);

        $dto = new CreateInvoiceDTO(
            $data->currency,
            $this->getPrice($data),
            'Продление тарифа'
        );

        $invoice = $gateway->invoice($dto, $customer);
        $subscription->update([
            'expired_at' => $data->extraUsersLimit + $subscription->extra_user_limit
        ]);

        return $this->response(
            message: 'success',
            data: $invoice
        );
    }
}
