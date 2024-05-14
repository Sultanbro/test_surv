<?php

namespace App\Http\Controllers\Payment;

use App\Facade\Payment\Gateway;
use App\Http\Controllers\Controller;
use App\Http\Requests\Payment\TariffSubscribeRequest;
use App\Jobs\ProcessCreatePaymentInvoiceLead;
use App\Models\CentralUser;
use App\Models\Tariff\PaymentToken;
use App\Models\Tariff\TariffSubscription;
use App\Service\Payments\Core\PaymentUpdateStatusService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function __construct(
        private readonly PaymentUpdateStatusService $updateStatusService
    )
    {
    }

    /**
     * Делаем оплату.
     *
     * @param TariffSubscribeRequest $request
     * @return JsonResponse
     * @throws Exception
     */
    public function payment(TariffSubscribeRequest $request): JsonResponse
    {
        $data = $request->toDto();

        $user = CentralUser::fromAuthUser();
        $gateway = Gateway::provider($data->currency);
        $response = $gateway->invoice($data, $user);
        $token = new PaymentToken($response->getPaymentToken()->token);
        $payment = TariffSubscription::subscribe($data, $token);

        ProcessCreatePaymentInvoiceLead::dispatch($user, $payment)
            ->onConnection('sync');

        return $this->response(
            message: 'Success',
            data: $response
        );
    }

    /**
     * Вебхук: коротый отправляется от сервиса опалты.
     * Тут мы должны проверить сигнатуру и сохранить статус оплаты
     *
     * @param Request $request
     * @param string $currency
     * @return JsonResponse
     * @throws Exception
     */
    public function callback(Request $request, string $currency): JsonResponse
    {
        $headers = $request->header();
        $fields = $request->all();
        $invoice = Gateway::provider($currency)
            ->report([
                'headers' => $headers,
                'fields' => $fields
            ])
            ->handle();

        return response()->json($invoice);
    }

    /**
     * Получаем статус и сохраняем в таблице tariff_payments.
     *
     * @return JsonResponse
     * @throws Exception
     */
    public function updateToTariffPayments(): JsonResponse
    {
        $user = CentralUser::fromAuthUser();
        $response = $this->updateStatusService->handle($user);

        return $this->response(
            message: 'Success',
            data: $response
        );
    }
}
