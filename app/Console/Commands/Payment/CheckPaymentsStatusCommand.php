<?php

namespace App\Console\Commands\Payment;

use App\Enums\Payments\PaymentStatusEnum;
use App\Models\Tariff\TariffPayment;
use App\Service\Payments\Core\PaymentFactory;
use App\User;
use Exception;
use Illuminate\Console\Command;

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
     * @var PaymentFactory
     */
    private PaymentFactory $factory;

    public function __construct()
    {
        parent::__construct();
        $this->factory = new PaymentFactory();
    }

    /**
     * Execute the console command.
     *
     * @throws Exception
     */
    public function handle(): void
    {
        $payments = TariffPayment::query()
            ->with('tenant')
            ->where('status', '=', PaymentStatusEnum::STATUS_PENDING)
            ->get();

        foreach ($payments as $payment) {
            try {
                $this->factory
                    ->getPaymentProviderByPayment($payment)
                    ->updateStatusByPayment($payment);
            } catch (Exception) {
                //TODO log exception
            }
        }
    }
}
