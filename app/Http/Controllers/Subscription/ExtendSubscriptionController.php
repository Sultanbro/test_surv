<?php

namespace App\Http\Controllers\Subscription;

use App\DTO\Payment\CreateInvoiceDTO;
use App\Enums\Invoice\InvoiceType;
use App\Facade\Payment\Gateway;
use App\Http\Controllers\Controller;
use App\Http\Requests\Subscription\UpdateSubscriptionRequest;
use App\Models\CentralUser;
use App\Models\Tariff\TariffSubscription;
use App\Service\Subscription\CanCalculateTariffPrice;
use App\Service\Tariff\TariffListService;
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
        $customer = CentralUser::fromAuthUser()->toCustomerDTO();
        $gateway = Gateway::provider($data->provider);

        $dto = new CreateInvoiceDTO(
            $data->currency,
            $this->getPrice($data),
            InvoiceType::EXTEND_SUBSCRIPTION,
            'Продление тарифа'
        );

        $invoice = $gateway->createNewInvoice($dto, $customer);
        $subscription->update([
            'expired_at' => $data->extraUsersLimit + $subscription->extra_user_limit
        ]);

        return $this->response(
            message: 'success',
            data: $invoice
        );
    }
}
