<?php

namespace App\Http\Controllers\Tariff;

use App\Facade\Payment\Gateway;
use App\Http\Controllers\Controller;
use App\Http\Requests\Payment\TariffSubscribeRequest;
use App\Models\CentralUser;
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
        $pipeline->apply(); // create payment invoice and subscribe to the tariff plan

        return $this->response(
            message: 'Success',
            data: $pipeline->invoice()
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
        $report = Gateway::provider($currency)
            ->report([
                'headers' => $headers,
                'fields' => $fields
            ])
            ->handle();

        return $this->response(
            message: "successful",
            data: $report
        );
    }

    /**
     * Получаем статус и сохраняем в таблице tariff_payments.
     *
     * @return JsonResponse
     * @throws Exception
     */
    public function status(): JsonResponse
    {
        $user = CentralUser::fromAuthUser();
        $response = $this->updateStatusService->handle($user);

        return $this->response(
            message: 'Success',
            data: $response
        );
    }
}
