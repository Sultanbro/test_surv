<?php

namespace App\Console\Commands\Payment;

use App\Enums\Payments\PaymentStatusEnum;
use App\Facade\Payment\Gateway;
use App\Models\Tariff\TariffPayment;
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
        /** @var Collection<TariffPayment> $payments */
        $payments = TariffPayment::query()
            ->with('tenant')
            ->where('status', '=', PaymentStatusEnum::STATUS_PENDING)
            ->get();

        foreach ($payments as $payment) {
            try {
                $gateway = Gateway::get($payment->service_for_payment);
                $gateway->updateStatusByPayment($payment);
            } catch (Exception) {
                //TODO log exception
            }
        }
    }
}
