<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Payment\DoPaymentRequest;
use App\Http\Requests\Api\Payment\StatusPaymentRequest;
use App\Service\Payments\PaymentFactory;
use Exception;
use Illuminate\Http\JsonResponse;

class PaymentController extends Controller
{
    /**
     * @param PaymentFactory $factory
     */
    public function __construct(public PaymentFactory $factory)
    {
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
     * @param StatusPaymentRequest $request
     * @return JsonResponse
     * @throws Exception
     */
    public function updateToTariffPayments(StatusPaymentRequest $request): JsonResponse
    {
        $dto = $request->toDto();
        $response = $this->factory->getPaymentProviderByPaymentId($dto->paymentId)->updateStatus($dto);

        return $this->response(
            message: 'Success',
            data: $response
        );
    }
}
