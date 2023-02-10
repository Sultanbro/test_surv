<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\DoPaymentRequest;
use App\Service\Payments\PaymentFactory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    /**
     * @param PaymentFactory $factory
     */
    public function __construct(public PaymentFactory $factory)
    {
    }

    /**
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
}
