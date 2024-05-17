<?php

namespace App\Console\Commands\Payment;

use App\Enums\Payments\PaymentStatusEnum;
use App\Facade\Payment\Gateway;
use App\Models\Tariff\TariffSubscription;
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
    protected $signature = 'check-payments-status:run';

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
        /** @var Collection<TariffSubscription> $payments */
        $payments = TariffSubscription::query()
            ->with('tenant')
            ->where('status', '=', PaymentStatusEnum::STATUS_PENDING)
            ->get();

        foreach ($payments as $payment) {
            try {
                $gateway = Gateway::provider($payment->payment_provider);
                $gateway->updateStatusByPayment($payment);
            } catch (Exception) {
                //TODO log exception
            }
        }
    }
}
