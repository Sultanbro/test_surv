<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\DoPaymentRequest;
use App\Http\Requests\Api\StatusPaymentRequest;
use App\Service\Payments\PaymentFactory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use YooKassa\Client;

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
        $response = $this->factory->getPayment($dto->currency)->pay($request->toDto());

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
     */
    public function status(StatusPaymentRequest $request): JsonResponse
    {
        $dto = $request->toDto();
        $response = $this->factory->getStatus($dto->paymentType)->status($dto);

        return $this->response(
            message: 'Success',
            data: $response
        );
    }
}
