<?php
declare(strict_types=1);

namespace App\Service\Payments\YooKassaConnectors;

use App\DTO\Api\DoPaymentDTO;
use App\Models\Tariff\Tariff;
use App\Service\Payments\PaymentTypeConnector;
use App\Traits\YooKassaTrait;
use App\User;
use Exception;
use Illuminate\Database\Eloquent\Model;
use naffiq\tenge\CurrencyRates;
use YooKassa\Client;
use YooKassa\Request\Payments\CreatePaymentResponse;

class YooKassaConnector implements PaymentTypeConnector
{
    /**
     * @var Client
     */
    private Client $client;

    public function __construct(
        public int $merchantId,
        public string $secretKey
    )
    {
        $this->client = new Client();
        $this->client->setAuth($this->merchantId, $this->secretKey);
    }

    /**
     * Делает оплату.
     *
     * @throws Exception
     */
    public function doPayment(DoPaymentDTO $dto): ?CreatePaymentResponse
    {
        try {
            $idempotenceKey = uniqid('', true);

            return $this->client->createPayment(
                $this->getPaymentRequest($dto->tariffId, $dto->extraUsersLimit, $dto->autoPayment),
                $idempotenceKey
            );

        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * @param int $tariffId
     * @param int $extraUsersLimit
     * @param bool $autoPayment
     * @return array
     * @throws Exception
     */
    private function getPaymentRequest(
        int $tariffId,
        int $extraUsersLimit,
        bool $autoPayment = false
    ): array
    {
        $tariff = Tariff::getTariffById($tariffId);
        $priceForOnePerson = env('PAYMENT_FOR_ONE_PERSON');
        return array(
            'amount' => array(
                'value'     => Tariff::calculateTotalPrice($tariff->id, $extraUsersLimit),
                'currency'  => 'RUB',
            ),
            'confirmation' => array(
                'type'          => 'redirect',
                'locale'        => 'ru_RU',
                'return_url'    => 'https://jobtron.org/',
            ),
            'capture'           => true,
            'description'       => 'Заказ №' . time(),
            'save_payment_method' => $autoPayment,
            'metadata' => array(
                'orderNumber'   => time()
            ),
            'receipt' => array(
                'customer' => array(
                    'full_name' => $this->getUser()->full_name,
                    'email'     => $this->getUser()->email,
                    'phone'     => $this->getUser()->phone
                ),
                'items' => array(
                    array(
                        'description'   =>  "Покупка тарифа $tariff->kind",
                        'quantity'      => 1,
                        'amount' => array(
                            'value'     => Tariff::calculateTotalPrice($tariff->id, $extraUsersLimit),
                            'currency'  => 'RUB'
                        ),
                        'vat_code' => '1',
                        'payment_mode' => 'full_payment',
                        'payment_subject' => 'service',
                        'supplier' => array(
                            'name' => 'string',
                            'phone' => 'string'
                        )
                    ),
                    array(
                        'description'   =>  "Кол-во пользователей: $extraUsersLimit, цена за одного пользователя $priceForOnePerson." ,
                        'quantity'      => $extraUsersLimit,
                        'amount' => array(
                            'value'     => Tariff::calculateTotalPrice($tariff->id, $extraUsersLimit),
                            'currency'  => 'RUB'
                        ),
                        'vat_code' => '1',
                        'payment_mode' => 'full_payment',
                        'payment_subject' => 'service',
                        'supplier' => array(
                            'name' => 'string',
                            'phone' => 'string'
                        )
                    ),
                )
            )
        );
    }

    /**
     * @throws Exception
     */
    private function converterToRub(
        int $price
    ): float
    {
        $rates = new CurrencyRates(CurrencyRates::URL_RATES_ALL);
        return $rates->convertFromTenge('RUB', $price);
    }

    /**
     * @return ?object
     */
    private function getUser(): ?object
    {
        $id = auth()->id() ?? 5;
        return User::query()->find($id);
    }
}