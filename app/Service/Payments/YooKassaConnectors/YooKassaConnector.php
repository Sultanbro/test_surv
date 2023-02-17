<?php
declare(strict_types=1);

namespace App\Service\Payments\YooKassaConnectors;

use App\DTO\Api\DoPaymentDTO;
use App\Enums\Payments\PaymentStatusEnum;
use App\Models\Tariff\Tariff;
use App\Service\Payments\PaymentTypeConnector;
use App\Traits\CurrencyTrait;
use App\User;
use Exception;
use Illuminate\Database\Eloquent\Model;
use naffiq\tenge\CurrencyRates;
use YooKassa\Client;
use YooKassa\Request\Payments\CreatePaymentResponse;

class YooKassaConnector implements PaymentTypeConnector
{
    use CurrencyTrait;

    /**
     * @param Client $client
     */
    public function __construct(
        public Client $client
    )
    {}

    /**
     * Делает оплату.
     *
     * @throws Exception
     */
    public function doPayment(DoPaymentDTO $dto, int $authUserId): ?CreatePaymentResponse
    {
        try {
            $idempotenceKey = uniqid('', true);

            $response =  $this->client->createPayment(
                $this->getPaymentRequest($dto->tariffId, $dto->extraUsersLimit, $authUserId, $dto->autoPayment),
                $idempotenceKey
            );

            // Check the status of the payment
            if ($response->getStatus() === PaymentStatusEnum::STATUS_CANCELED || $response->getStatus() === PaymentStatusEnum::STATUS_FAILED) {
                throw new \Exception('Payment failed');
            }

            return $response;
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * @param int $tariffId
     * @param int $extraUsersLimit
     * @param int $authUserId
     * @param bool $autoPayment
     * @return array
     * @throws Exception
     */
    private function getPaymentRequest(
        int $tariffId,
        int $extraUsersLimit,
        int $authUserId,
        bool $autoPayment = false
    ): array
    {
        $tariff = Tariff::getTariffById($tariffId);
        $priceForOnePerson = env('PAYMENT_FOR_ONE_PERSON');
        $user   = User::getAuthUser($authUserId);
        $price  = $tariff->calculateTotalPrice($tariff->id, $extraUsersLimit);
        $priceToRub = $this->converterToRub($price);
        $origin = request()->headers->get('origin');

        return array(
            'amount' => array(
                'value'     => $priceToRub,
                'currency'  => 'RUB',
            ),
            'confirmation' => array(
                'type'          => 'redirect',
                'locale'        => 'ru_RU',
                'return_url'    => $origin . '/pricing?status=1',
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
                            'value'     => $priceToRub,
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
                            'value'     => $priceToRub,
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
}