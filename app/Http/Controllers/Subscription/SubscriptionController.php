<?php

namespace App\Http\Controllers\Subscription;

use App\DTO\Payment\CreateInvoiceDTO;
use App\Facade\Payment\Gateway;
use App\Http\Controllers\Controller;
use App\Http\Requests\Subscription\UpdateSubscriptionRequest;
use App\Models\CentralUser;
use App\Service\Admin\Owners\OwnerInfoService;
use App\Service\Payment\Core\CanCalculateTariffPrice;
use App\Service\Payment\Core\TariffListService;
use Exception;
use Illuminate\Http\JsonResponse;

class SubscriptionController extends Controller
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
        $gateway = Gateway::provider($data->provider);

        $dto = new CreateInvoiceDTO(
            $data->currency,
            $this->getPrice($data),
            'Рашерение тарифа'
        );

        $invoice = $gateway->invoice($dto, $customer);

        return $this->response(
            message: 'success',
            data: $invoice
        );
    }
}
