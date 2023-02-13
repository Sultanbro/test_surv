<?php

namespace App\Console\Commands\Api;

use App\Models\Tariff\Tariff;
use App\Models\Tariff\TariffPayment;
use App\Service\Payments\PaymentFactory;
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
     * @var Client
     */
    private Client $client;

    public function __construct()
    {
        parent::__construct();
        $this->client = new Client();
        $this->client->setAuth(config('yookassa')['test_merchant_id'], config('yookassa')['test_secret_key']);
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
            $method = $payment->service_for_payment . 'AutoPayment';
            if (!method_exists($this, $method))
            {
                throw new Exception("Method $method not defined, please create");
            }

            return $this->{$method}($payment);
        }
    }

    /**
     * @throws NotFoundException
     * @throws ResponseProcessingException
     * @throws ApiException
     * @throws BadApiRequestException
     * @throws ExtensionNotFoundException
     * @throws InternalServerError
     * @throws ForbiddenException
     * @throws TooManyRequestsException
     * @throws UnauthorizedException
     * @throws Exception
     */
    private function yookassaAutoPayment(
        TariffPayment $payment
    ): void
    {
        $this->client->createPayment(
            array(
                'amount' => array(
                    'value' => $this->tariffPrice($payment->tariff_id),
                    'currency' => 'RUB',
                ),
                'capture' => true,
                'payment_method_id' => $payment->payment_id,
                'description' => 'Заказ №' . time(),
            ),
            uniqid('', true)
        );

        $this->createTariffPayment($payment);
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
