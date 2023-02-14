<?php
declare(strict_types=1);

namespace App\Service\Payments\YooKassaConnectors;

use App\DTO\Api\DoPaymentDTO;
use App\Models\Tariff\Tariff;
use App\Service\Payments\PaymentTypeConnector;
use App\User;
use Exception;
use Illuminate\Database\Eloquent\Model;
use naffiq\tenge\CurrencyRates;
use YooKassa\Client;
use YooKassa\Request\Payments\CreatePaymentResponse;

class YooKassaConnector implements PaymentTypeConnector
{
    /**
     * @param int $merchantId
     * @param string $secretKey
     * @param Client $client
     */
    public function __construct(
        public int $merchantId,
        public string $secretKey,
        public Client $client
    )
    {
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
        $user = User::getAuthUser();
        $price = $tariff->calculateTotalPrice($tariff->id, $extraUsersLimit);
        return array(
            'amount' => array(
                'value'     => $price,
                'currency'  => 'RUB',
            ),
            'confirmation' => array(
                'type'          => 'redirect',
                'locale'        => 'ru_RU',
                'return_url'    => 'https://jobtron.org/payment/',
            ),
            'capture'           => true,
            'description'       => 'Заказ №' . time(),
            'save_payment_method' => $autoPayment,
            'metadata' => array(
                'orderNumber'   => time()
            ),
            'receipt' => array(
                'customer' => array(
                    'full_name' => $user->full_name,
                    'email'     => $user->email,
                    'phone'     => $user->phone
                ),
                'items' => array(
                    array(
                        'description'   =>  "Покупка тарифа $tariff->kind",
                        'quantity'      => 1,
                        'amount' => array(
                            'value'     => $price,
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
                            'value'     => $price,
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
}