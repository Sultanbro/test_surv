<?php

namespace App\Http\Controllers\Subscription;

use App\Facade\Payment\Gateway;
use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CallbackController extends Controller
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
        $response = Gateway::provider($currency)
            ->webhook([
                'headers' => $request->header(),
                'fields' => $request->all()
            ])
            ->handle();

        return response()->json($response);
    }
}
