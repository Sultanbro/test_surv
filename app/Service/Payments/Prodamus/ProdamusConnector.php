<?php
declare(strict_types=1);

namespace App\Service\Payments\Prodamus;

use App\DTO\Api\PaymentDTO;
use App\Models\CentralUser;
use App\Models\Tariff\Tariff;
use App\Models\Tariff\TariffPrice;
use App\Service\Payments\Core\ConfirmationResponse;
use App\Service\Payments\Core\HasIdempotenceKey;
use App\Service\Payments\Core\HasPriceConverter;
use App\Service\Payments\Core\PaymentConnector;
use BeGateway\GetPaymentToken;
use Exception;

class ProdamusConnector implements PaymentConnector
{
    use HasIdempotenceKey;
    use HasPriceConverter;

    public function __construct(
        private readonly string $shopKey,
        private readonly string $successUrl,
        private readonly string $failUrl
    )
    {
    }

    /**
     * Делает оплату.
     *
     * @throws Exception
     */
    public function pay(PaymentDTO $data, CentralUser $user): ConfirmationResponse
    {
//        try {
        $this->buildRequest($data, $user);
        $response = $this->client->submit();
        // Check the status of the payment
        if (!$response->isSuccess()) {
            throw new Exception($response->getMessage());
        }

        return new ConfirmationResponse(
            $response->getRedirectUrl(),
            $this->client->getTrackingId(),
            $response->isSuccess()
        );

//        } catch (Exception $e) {
//            throw new Exception($e->getMessage());
//        }
    }

    /**
     * @param PaymentDTO $data
     * @param CentralUser $authUser
     * @return void
     */
    private function buildRequest(
        PaymentDTO  $data,
        CentralUser $authUser
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
        $this->client->customer->setCity($authUser->city);
        $this->client->customer->setIp(request()->ip());
    }
}