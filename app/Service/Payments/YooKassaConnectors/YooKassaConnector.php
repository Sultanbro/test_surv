<?php
declare(strict_types=1);

namespace App\Service\Payments\YooKassaConnectors;

use App\DTO\Api\DoPaymentDTO;
use App\Enums\Payments\PaymentStatusEnum;
use App\Models\Tariff\Tariff;
use App\Service\Payments\PaymentTypeConnector;
use App\User;
use Exception;
use Illuminate\Database\Eloquent\Model;
use YooKassa\Client;
use YooKassa\Model\CurrencyCode;
use YooKassa\Request\Payments\CreatePaymentRequestInterface;
use YooKassa\Request\Payments\CreatePaymentResponse;
use YooKassa\Request\Payments\CreatePaymentRequest;

class YooKassaConnector implements PaymentTypeConnector
{
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
     * @return CreatePaymentRequestInterface
     * @throws Exception
     */
    private function getPaymentRequest(
        int $tariffId,
        int $extraUsersLimit,
        int $authUserId,
        bool $autoPayment = false
    ): CreatePaymentRequestInterface
    {
        $tariff = Tariff::getTariffById($tariffId);
        $user   = User::getAuthUser($authUserId);
        $price  = $tariff
            ->getPrice($extraUsersLimit)
            ->setCurrency('rub');
        $origin = request()->headers->get('origin');

        $builder = CreatePaymentRequest::builder();

        $builder->setAmount(array(
            'value' => $price->getTotal(),
            'currency' => CurrencyCode::RUB,
        ));
        $builder->setConfirmation(array(
            'type'       => 'redirect',
            'locale'     => 'ru_RU',
            'return_url' => $origin . '/pricing?status=1',
        ));
        $builder->setCapture(true);
        $builder->setDescription('Заказ №' . time());
        $builder->setSavePaymentMethod($autoPayment);
        $builder->setMetadata(array(
            'orderNumber'   => time()
        ));
        $builder->setReceipt($price->createYooKassaReceipt($user));

        return $builder->build();
    }
}