<?php

namespace App\Http\Controllers\Subscription;

use App\DTO\Payment\CreateInvoiceDTO;
use App\Enums\Invoice\InvoiceType;
use App\Facade\Payment\Gateway;
use App\Http\Controllers\Controller;
use App\Http\Requests\Subscription\UpdateSubscriptionRequest;
use App\Models\CentralUser;
use App\Models\Invoice;
use App\Models\Tariff\Tariff;
use App\Models\Tariff\TariffSubscription;
use App\Service\Subscription\CanCalculateTariffPrice;
use App\Service\Tariff\TariffListService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Carbon;

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
        $validates = [
            'monthly' => 1,
            'threeMonthly' => 3,
            'yearly' => 12,
        ];

        $data = $request->toDto();
        $customer = CentralUser::fromAuthUser()->toCustomerDTO();
        $gateway = Gateway::provider($data->provider);
        /** @var Tariff $tariff */
        $tariff = Tariff::query()->findOrFail($data->tariffId);

        $dto = new CreateInvoiceDTO(
            $data->currency,
            $this->getPrice($data),
            InvoiceType::EXTEND_SUBSCRIPTION,
            'Продление тарифа'
        );

        $invoiceResponse = $gateway->createNewInvoice($dto, $customer);
        dd(
            $subscription->expire_date,
            $validates[$tariff->validity],
            Carbon::create($subscription->expire_date)->addMonths($validates[$tariff->validity])->format('Y-m-d')
        );
        Invoice::query()->create([
            'payer_name' => auth()->user()->name,
            'payer_phone' => auth()->user()->phone,
            'name' => $dto->description,
            'url' => $invoiceResponse->getUrl(),
            'provider' => $gateway->name(),
            'status' => 'pending',
            'type' => InvoiceType::EXTEND_SUBSCRIPTION,
            'payload' => [
                'subscription_id' => $subscription->id,
                'extra_user_limit' => $subscription->extra_user_limit + $data->extraUsersLimit,
                'expired_at' => Carbon::create($subscription->expire_date)->addMonths($tariff->validity)->format('Y-m-d'),
            ],
            'transaction_id' => $invoiceResponse->getTransaction()->id
        ]);

        return $this->response(
            message: 'success',
            data: $invoiceResponse
        );
    }
}
