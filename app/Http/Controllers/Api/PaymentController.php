<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Payment\DoPaymentRequest;
use App\Service\Payments\PaymentFactory;
use App\Service\Payments\PaymentUpdateStatusService;
use Exception;
use Illuminate\Http\JsonResponse;

class PaymentController extends Controller
{
    public PaymentUpdateStatusService $updateStatusService;

    /**
     * @param PaymentFactory $factory
     */
    public function __construct(public PaymentFactory $factory)
    {
        $this->updateStatusService = new PaymentUpdateStatusService($factory);
    }

    /**
     * Делаем оплату.
     *
     * @param DoPaymentRequest $request
     * @return JsonResponse
     */
    public function payment(DoPaymentRequest $request): JsonResponse
    {
        $dto = $request->toDto();
        $response = $this->factory->getPaymentsProviderByCurrency($dto->currency)->pay($request->toDto());

        return $this->response(
            message: 'Success',
            data:  $response
        );
    }

    /**
     * Получаем статус и сохраняем в таблице tariff_payments.
     *
     * @return JsonResponse
     * @throws Exception
     */
    public function updateToTariffPayments(): JsonResponse
    {
        $ownerId = auth()->id(); // validated by middleware guard
        $response = $this->updateStatusService->handle($ownerId);

        return $this->response(
            message: 'Success',
            data: $response
        );
    }
}
