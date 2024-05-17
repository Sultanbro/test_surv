<?php

namespace App\Http\Controllers\Subscription;

use App\DTO\Payment\CreateInvoiceDTO;
use App\Facade\Payment\Gateway;
use App\Http\Controllers\Controller;
use App\Http\Requests\Subscription\UpdateSubscriptionRequest;
use App\Models\CentralUser;
use App\Models\Tariff\TariffSubscription;
use App\Service\Admin\Owners\OwnerInfoService;
use App\Service\Payment\Core\TariffListService;
use Exception;
use Illuminate\Http\JsonResponse;

class SubscriptionController extends Controller
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
    public function get(): JsonResponse
    {
        /** @var OwnerInfoService $service */
        $service = app(OwnerInfoService::class);

        return $this->response(
            message: 'success',
            data: $service->handle()
        );
    }

    public function update(UpdateSubscriptionRequest $request): JsonResponse
    {
        $data = $request->toDto();
        $customer = CentralUser::fromAuthUser()->customer();
        $existingSubscription = TariffSubscription::getValidTariffPayment($data->tenantId);
        $gateway = Gateway::provider($existingSubscription->payment_provider);

        $invoice = new CreateInvoiceDTO(
            $gateway->currency(),
            $existingSubscription->tariff_id,
            $data->tenantId,
            $data->extraUsersLimit,
            $gateway->name()
        );

        $invoice = Gateway::provider($existingSubscription->payment_provider)->invoice($invoice, $customer);

        return $this->response(
            message: 'success',
            data: $invoice
        );
    }
}
