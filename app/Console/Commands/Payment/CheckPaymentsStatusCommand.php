<?php

namespace App\Console\Commands\Payment;

use App\DTO\PaymentEventDTO;
use App\Enums\Invoice\InvoiceType;
use App\Enums\Payments\PaymentStatusEnum;
use App\Events\Payment\ExtendSubscription;
use App\Events\Payment\NewPracticumInvoiceShipped;
use App\Events\Payment\NewSubscription;
use App\Events\Payment\UpdateSubscription;
use App\Facade\Payment\Gateway;
use App\Models\Invoice;
use App\Service\Payment\Core\Webhook\WebhookDto;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Collection;

class CheckPaymentsStatusCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'payment:status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Запускается для обновления статусов оплаты';

    /**
     * Execute the console command.
     *
     * @throws Exception
     */
    public function handle(): void
    {
        /** @var Collection<Invoice> $payments */
        $payments = Invoice::query()
            ->where('status', '=', PaymentStatusEnum::STATUS_PENDING)
            ->get();

        foreach ($payments as $payment) {
            try {
                $gateway = Gateway::provider($payment->payment_provider);
                $gateway->statusManager()->getStatus($payment);
                $successStatus = $gateway->statusManager()->getStatus($payment) === PaymentStatusEnum::STATUS_SUCCESS;
                $dto = new PaymentEventDTO($payment->id, $successStatus, $gateway->name(), $payment->type);
                match ($payment->type) {
                    InvoiceType::NEW_SUBSCRIPTION    => NewSubscription::dispatch($dto),
                    InvoiceType::EXTEND_SUBSCRIPTION => ExtendSubscription::dispatch($dto),
                    InvoiceType::UPDATE_SUBSCRIPTION => UpdateSubscription::dispatch($dto),
                    InvoiceType::PRACTICUM           => NewPracticumInvoiceShipped::dispatch($dto),
                    InvoiceType::SWITCH_SUBSCRIPTION => throw new Exception('To be implemented'),
                };
            } catch (Exception) {

            }
        }
    }
}
