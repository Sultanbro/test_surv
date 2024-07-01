<?php

namespace App\Http\Controllers\Subscription;

use App\DTO\Payment\CreateInvoiceDTO;
use App\Enums\Invoice\InvoiceType;
use App\Facade\Payment\Gateway;
use App\Http\Controllers\Controller;
use App\Http\Requests\Subscription\UpdateSubscriptionRequest;
use App\Models\CentralUser;
use App\Models\Invoice;
use App\Models\Tariff\TariffSubscription;
use App\Service\Subscription\CanCalculateTariffPrice;
use App\Service\Tariff\TariffListService;
use Illuminate\Http\JsonResponse;

class UpdateSubscriptionController extends Controller
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
            $this->getPriceForExtraUsers($data),
            InvoiceType::UPDATE_SUBSCRIPTION,
            'Рашерение тарифа'
        );

        $invoiceResponse = $gateway->createNewInvoice($dto, $customer);

        Invoice::query()->create([
            'payer_name' => auth()->user()->name,
            'payer_phone' => auth()->user()->phone,
            'name' => $dto->description,
            'url' => $invoiceResponse->getUrl(),
            'provider' => $gateway->name(),
            'status' => 'pending',
            'type' => InvoiceType::UPDATE_SUBSCRIPTION,
            'payload' => [
                'subscription_id' => $subscription->id,
                'extra_user_limit' => $subscription->extra_user_limit + $data->extraUsersLimit
            ]
        ]);

        return $this->response(
            message: 'success',
            data: $invoiceResponse
        );
    }
}
