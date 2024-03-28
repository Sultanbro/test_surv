<?php
declare(strict_types=1);

namespace App\Service\Payments\Prodamus;

use App\DTO\Api\PaymentDTO;
use App\Models\Tariff\Tariff;
use App\Models\Tariff\TariffPrice;
use App\Service\Payments\Core\ConfirmationResponse;
use App\Service\Payments\Core\PaymentConnector;
use App\User;
use BeGateway\GetPaymentToken;
use Exception;

class ProdamusConnector implements PaymentConnector
{
    public function __construct(
        private readonly GetPaymentToken $client
    )
    {
    }

    /**
     * Делает оплату.
     *
     * @throws Exception
     */
    public function pay(PaymentDTO $data, User $user): ConfirmationResponse
    {
        try {
            $this->buildRequest($data, $user);
            $response = $this->client->submit();

            // Check the status of the payment
            if (!$response->isSuccess()) {
                throw new Exception('Payment failed');
            }

            return new ConfirmationResponse($response->getRedirectUrl(), $response->getToken());
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * @param PaymentDTO $data
     * @param User $authUser
     * @return void
     * @throws Exception
     */
    private function buildRequest(
        PaymentDTO $data,
        User       $authUser
    ): void
    {
        $price = $this->getPrice($data);
        $idempotenceKey = $this->generateIdempotenceKey();

        $this->client->setTrackingId($idempotenceKey);
        $this->client->money->setAmount($price->getTotal());
        $this->client->setDescription('Заказ №' . time());
        $this->client->customer->setFirstName($authUser->name);
        $this->client->customer->setLastName($authUser->last_name);
        $this->client->customer->setEmail($authUser->email);
        $this->client->customer->setCountry($authUser->working_country);
        $this->client->customer->setAddress($authUser->address);
        $this->client->customer->setCity($authUser->city);
        $this->client->customer->setIp(request()->ip());
    }

    private function getPrice(PaymentDTO $data): TariffPrice
    {
        $tariff = Tariff::getTariffById($data->tariffId);
        return $tariff
            ->getPrice($data->extraUsersLimit)
            ->setCurrency('rub');
    }

    private function generateIdempotenceKey(): string
    {
        return $idempotenceKey = uniqid('', true);
    }
}