<?php
declare(strict_types=1);

namespace App\Service\Payments\YooKassaConnectors;

use App\DTO\Api\PaymentDTO;
use App\Enums\Payments\PaymentStatusEnum;
use App\Models\Tariff\Tariff;
use App\Service\Payments\Core\PaymentConnector;
use App\User;
use Exception;
use YooKassa\Client;
use YooKassa\Model\CurrencyCode;
use YooKassa\Request\Payments\CreatePaymentRequest;
use YooKassa\Request\Payments\CreatePaymentRequestInterface;
use YooKassa\Request\Payments\CreatePaymentResponse;

class YooKassaConnector implements PaymentConnector
{
    /**
     * @param Client $client
     */
    public function __construct(
        public Client $client
    )
    {
    }

    /**
     * Делает оплату.
     *
     * @throws Exception
     */
    public function pay(PaymentDTO $data, User $user): ?CreatePaymentResponse
    {
        try {
            $idempotenceKey = uniqid('', true);
            $buildRequest = $this->getPaymentRequest($data->tariffId, $data->extraUsersLimit, $user, $data->autoPayment);

            $response = $this->client->createPayment(
                $buildRequest,
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
     * @param User $authUser
     * @param bool $autoPayment
     * @return CreatePaymentRequestInterface
     * @throws Exception
     */
    private function getPaymentRequest(
        int  $tariffId,
        int  $extraUsersLimit,
        User $authUser,
        bool $autoPayment = false
    ): CreatePaymentRequestInterface
    {
        $tariff = Tariff::getTariffById($tariffId);
        $price = $tariff
            ->getPrice($extraUsersLimit)
            ->setCurrency('rub');
        $origin = request()->headers->get('origin');

        $builder = CreatePaymentRequest::builder();
        $builder->setAmount(array(
            'value' => $price->getTotal(),
            'currency' => CurrencyCode::RUB,
        ));
        $builder->setConfirmation(array(
            'type' => 'redirect',
            'locale' => 'ru_RU',
            'return_url' => $origin . '/pricing?status=1',
        ));
        $builder->setCapture(true);
        $builder->setDescription('Заказ №' . time());
        $builder->setSavePaymentMethod($autoPayment);
        $builder->setMetadata(array(
            'orderNumber' => time()
        ));
        $builder->setReceipt($price->createYoKassReceipt($authUser));

        return $builder->build();
    }
}