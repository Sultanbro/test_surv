<?php

namespace App\Http\Controllers\Payment;

use App\Facade\Payment\Gateway;
use App\Http\Controllers\Controller;
use App\Http\Requests\Payment\TariffSubscribeRequest;
use App\Service\Payments\Core\PaymentUpdateStatusService;
use App\Service\Payments\Pipeline\PaymentPipeline;
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
        $pipeline = new PaymentPipeline($request->toDto());
        $pipeline->apply();

        return $this->response(
            message: 'Success',
            data: $pipeline->invoice()
        );
    }

    /**
     * Вебхук: коротый отправляется от сервиса опалты.
     * Тут мы должны проверить сигнатуру и сохранить статус оплаты
     * После чего вернуть ответ об статусе транзакции
     *
     * @param Request $request
     * @param string $currency
     * @return JsonResponse
     * @throws Exception
     */
    public function callback(Request $request, string $currency): JsonResponse
    {
        $response = Gateway::provider($currency)
            ->webhook([
                'headers' => $request->header(),
                'fields' => $request->all()
            ])
            ->handle();

        return response()->json($response);
    }

    /**
     * Получаем статус и сохраняем в таблице tariff_payments.
     *
     * @return JsonResponse
     * @throws Exception
     */
    public function updateToTariffPayments(): JsonResponse
    {
        $response = $this->updateStatusService->handle(tenant('id'));

        return $this->response(
            message: 'Success',
            data: $response
        );
    }
}
