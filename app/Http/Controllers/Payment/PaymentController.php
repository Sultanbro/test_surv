<?php

namespace App\Http\Controllers\Payment;

use App\Facade\Payment\Gateway;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Payment\DoPaymentRequest;
use App\Jobs\ProcessCreatePaymentInvoiceLead;
use App\Models\CentralUser;
use App\Models\Tariff\PaymentToken;
use App\Models\Tariff\Tariff;
use App\Models\Tariff\TariffPayment;
use App\Service\Payments\Core\PaymentFactory;
use App\Service\Payments\Core\PaymentGatewayRegistry;
use App\Service\Payments\Core\PaymentUpdateStatusService;
use App\User;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
     * @param DoPaymentRequest $request
     * @return JsonResponse
     * @throws Exception
     */
    public function payment(DoPaymentRequest $request): JsonResponse
    {
        $data = $request->toDto();

        $user = CentralUser::fromAuthUser();
        $gateway = Gateway::get($data->currency);
        $response = $gateway->pay($data, $user);
        $token = new PaymentToken($response->getPaymentId());
        $payment = TariffPayment::savePayment($data, $token);

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
     */
    public function callback(Request $request, string $currency): JsonResponse
    {
        $headers = $request->header();
        $fields = $request->all();
        $invoice = Gateway::get($currency)
            ->invoice([
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
