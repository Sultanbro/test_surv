<?php

namespace App\Console\Commands\Api;

use App\Models\Tariff\Tariff;
use App\Models\Tariff\TariffPayment;
use App\Service\Payments\PaymentFactory;
use App\Service\Payments\YooKassaConnectors\YooKassa;
use App\Traits\YooKassaTrait;
use Exception;
use Illuminate\Console\Command;
use naffiq\tenge\CurrencyRates;
use YooKassa\Client;
use YooKassa\Common\Exceptions\ApiException;
use YooKassa\Common\Exceptions\BadApiRequestException;
use YooKassa\Common\Exceptions\ExtensionNotFoundException;
use YooKassa\Common\Exceptions\ForbiddenException;
use YooKassa\Common\Exceptions\InternalServerError;
use YooKassa\Common\Exceptions\NotFoundException;
use YooKassa\Common\Exceptions\ResponseProcessingException;
use YooKassa\Common\Exceptions\TooManyRequestsException;
use YooKassa\Common\Exceptions\UnauthorizedException;

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
     * @var YooKassa
     */
    private YooKassa $yooKassa;

    /**
     * @var PaymentFactory
     */
    private PaymentFactory $factory;

    public function __construct()
    {
        parent::__construct();
        $this->yooKassa = new YooKassa();
        $this->factory = new PaymentFactory();
    }

    /**
     * Execute the console command.
     *
     * @throws Exception
     */
    public function handle()
    {
        $payments = TariffPayment::query()->where([
            ['auto_payment', '=', true],
            ['expire_date', '=', now()->format('Y-m-d')]
        ])->get();

        foreach ($payments as $payment)
        {
            $this->factory->getPaymentsProviderByType($payment->service_for_payment)->autoPayment($payment);
            $this->createTariffPayment($payment);
        }
    }

    /**
     * @throws Exception
     */
    private function createTariffPayment(
        TariffPayment $payment
    ): void
    {
        TariffPayment::createPaymentOrFail(
            $payment->tariff_id,
            $payment->extra_user_limit,
            Tariff::calculateExpireDate($payment->tariff_id),
            $payment->payment_id,
            $payment->service_for_payment,
            $payment->auto_payment
        );
    }
    /**
     * @param int $tariffId
     * @return float
     * @throws Exception
     */
    private function tariffPrice(
        int $tariffId
    ): float
    {
        $tariff = Tariff::getTariffById($tariffId);
        $rates = new CurrencyRates(CurrencyRates::URL_RATES_ALL);
        return $rates->convertFromTenge('RUB', $tariff->price);
    }
}
