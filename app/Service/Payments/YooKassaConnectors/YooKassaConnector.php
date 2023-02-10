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

class YooKassaConnector implements PaymentTypeConnector
{
    /**
     * @var int
     */
    private int $merchantId;

    /**
     * @var string
     */
    private string $secretKey;

    /**
     * @var Client
     */
    private Client $client;

    /**
     * @param int $merchantId
     * @param string $secretKey
     */
    public function __construct(
        int $merchantId,
        string $secretKey
    )
    {
        $this->merchantId   = $merchantId;
        $this->secretKey    = $secretKey;
        $this->client       = new Client();
    }

    /**
     * Делает оплату.
     *
     * @throws Exception
     */
    public function doPayment(DoPaymentDTO $dto): string
    {
        $this->client->setAuth($this->merchantId, $this->secretKey);
        try {
            $idempotenceKey = uniqid('', true);
            $response = $this->client->createPayment(
                $this->getPaymentRequest($dto),
                $idempotenceKey
            );

            //получаем confirmationUrl для дальнейшего редиректа
            return $response->getConfirmation()->getConfirmationUrl();
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * @param DoPaymentDTO $dto
     * @return array
     * @throws Exception
     */
    private function getPaymentRequest(DoPaymentDTO $dto): array
    {
        $tariff = Tariff::getTariffById($dto->tariffId);
        $this->converterToRub((int)$tariff->price);
        return array(
            'amount' => array(
                'value'     => $this->converterToRub((int)$tariff->price),
                'currency'  => 'RUB',
            ),
            'confirmation' => array(
                'type'          => 'redirect',
                'locale'        => 'ru_RU',
                'return_url'    => 'https://jobtron.org/',
            ),
            'capture'           => true,
            'description'       => 'Заказ №' . time(),
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
                        'quantity'      => $dto->extraUsersLimit,
                        'amount' => array(
                            'value'     => $this->converterToRub((int)$tariff->price),
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
        return User::query()->find(5);
    }
}