<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Payment\DoPaymentRequest;
use App\Service\Payments\PaymentFactory;
use App\Service\Payments\PaymentUpdateStatusService;
use App\User;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

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
     * @throws Exception
     */
    public function payment(DoPaymentRequest $request): JsonResponse
    {
        $dto = $request->toDto();
        /** @var User $authUser */
        $authUser = Auth::user();
        $response = $this->factory->getPaymentsProviderByCurrency($dto->currency)->pay($request->toDto(), $authUser);

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
        $owner = User::getAuthUser($ownerId);
        $response = $this->updateStatusService->handle($owner);

        return $this->response(
            message: 'Success',
            data: $response
        );
    }
}
