<?php

namespace App\Http\Controllers\Payment;

use App\Facade\Payment\Gateway;
use App\Http\Controllers\Controller;
use App\Service\Payment\Core\Webhook\WebhookResponse;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WebhookController extends Controller
{
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
        slack(json_encode([
            'params' => $request->all(),
            'currency' => $currency
        ]));

        $gateway = Gateway::provider($currency);

        if ($gateway->currency() == 'kzt') {
            return response()->json(new WebhookResponse(['WMI_RESULT' => 'RETRY']));
        }

        $resp = $gateway->webhook()
            ->handle([
                'headers' => $request->header(),
                'fields' => $request->all()
            ]);

        return response()->json($resp);
    }
}
