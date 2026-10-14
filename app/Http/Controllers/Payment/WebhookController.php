<?php

namespace App\Http\Controllers\Payment;

use App\DTO\PaymentEventDTO;
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
        $invoice = Invoice::query()
            ->where('transaction_id', $webhookHandler->getTransactionId())
            ->where('provider', $provider->name())
            ->where('status', 'pending')
            ->first();

        if (!$invoice) return response()->json(['message' => 'invoice not found']);

        $eventDTO = new PaymentEventDTO(
            $invoice->id,
            $webhookHandler->InvoiceSuccessfullyHandled(),
            $provider->name(),
            $invoice->type
        );

        match ($invoice->type) {
            InvoiceType::NEW_SUBSCRIPTION => NewSubscription::dispatch($eventDTO),
            InvoiceType::EXTEND_SUBSCRIPTION => ExtendSubscription::dispatch($eventDTO),
            InvoiceType::UPDATE_SUBSCRIPTION => UpdateSubscription::dispatch($eventDTO),
            InvoiceType::PRACTICUM => NewPracticumInvoiceShipped::dispatch($eventDTO),
            InvoiceType::SWITCH_SUBSCRIPTION => throw new Exception('To be implemented'),
        };

        return response()->json($provider->staticWebhookResponse()); // to prevent overlapping
    }
}
