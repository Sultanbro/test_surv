<?php

namespace App\Http\Controllers\Payment;

use App\Events\Payment\PaymentWebhookTriggeredEvent;
use App\Http\Controllers\Controller;
use App\Service\Payment\Core\Webhook\WebhookDto;
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
        PaymentWebhookTriggeredEvent::dispatch(new WebhookDto($currency, $request->all(), $request->header()));

        return response()->json();
    }
}
