<?php

namespace App\Console\Commands\Payment;

use App\Models\Tariff\TariffPayment;
use App\Service\Payments\PaymentFactory;
use Exception;
use Illuminate\Console\Command;

class RunAutoPaymentCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'auto-payment:run';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Запускается для пользователей у кого включен авто-оплата';


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
            ->orderBy('expire_date', 'desc')
            ->where([
            ['auto_payment', '=', true],
            ['expire_date', '<=', now()->format('Y-m-d')]
        ])->get()->unique('owner_id');

        foreach ($payments as $payment)
        {
            $this->factory->getPaymentsProviderByType($payment->service_for_payment)->doAutoPayment($payment);
        }
    }
}
