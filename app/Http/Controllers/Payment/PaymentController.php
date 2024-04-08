<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Payment\DoPaymentRequest;
use App\Models\CentralUser;
use App\Service\Payments\Core\PaymentFactory;
use App\Service\Payments\Core\PaymentUpdateStatusService;
use App\User;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PaymentController extends Controller
{
    public PaymentUpdateStatusService $updateStatusService;

    /**
     * @param PaymentFactory $factory
     */
    public function __construct(private readonly PaymentFactory $factory)
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
        $data = $request->toDto();
        /** @var CentralUser $authUser */
        $authUser = CentralUser::query()->where('email', Auth::user()->email)->firstOrFail();
        $provider = $this->factory->currencyProvider($data->currency);
        $response = $provider->pay($data, $authUser);

        return $this->response(
            message: 'Success',
            data: $response
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
        /** @var CentralUser $authUser */
        $authUser = CentralUser::query()->where('email', Auth::user()->email)->firstOrFail();
        $response = $this->updateStatusService->handle($authUser);

        return $this->response(
            message: 'Success',
            data: $response
        );
    }

    public function callback(Request $request, string $currency): JsonResponse
    {
        $headers = $request->header();
        $fields = $request->all();
        $response = $this->factory
            ->currencyProvider($currency)
            ->invoice([
                'headers' => $headers,
                'fields' => $fields
            ])
            ->handle();

        return response()->json($response);
    }
}
