<?php

namespace App\Http\Controllers\Payment;

use App\Enums\Invoice\InvoiceType;
use App\Events\Payment\ExtendSubscription;
use App\Events\Payment\NewSubscription;
use App\Events\Payment\NewPracticumInvoiceShipped;
use App\Events\Payment\UpdateSubscription;
use App\Facade\Payment\Gateway;
use App\Http\Controllers\Controller;
use App\Models\Invoice;
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
        $provider = Gateway::provider($currency);
        $webhookHandler = $provider->webhookHandler();

        $dto = new WebhookDto($currency, $request->all(), $request->header());

        $webhookHandler->map([
            'params' => $dto->payload,
            'headers' => $dto->headers,
        ]);

        /** @var Invoice $invoice */
        $invoice = Invoice::query()->where([
            'transaction_id' => $webhookHandler->getTransactionId(),
            'provider' => $provider->name()
        ])->first();

        if (!$invoice) return response()->json(['message' => 'invoice not found']);

        match ($invoice->type) {
            InvoiceType::NEW_SUBSCRIPTION => NewSubscription::dispatch($dto),
            InvoiceType::EXTEND_SUBSCRIPTION => ExtendSubscription::dispatch($dto),
            InvoiceType::UPDATE_SUBSCRIPTION => UpdateSubscription::dispatch($dto),
            InvoiceType::PRACTICUM => NewPracticumInvoiceShipped::dispatch($dto),
            InvoiceType::SWITCH_SUBSCRIPTION => throw new Exception('To be implemented'),
        };

        return response()->json($provider->staticWebhookResponse()); // to prevent overlapping
    }
}
