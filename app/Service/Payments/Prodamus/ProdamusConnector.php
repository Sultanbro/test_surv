<?php
declare(strict_types=1);

namespace App\Service\Payments\Prodamus;

use App\DTO\Api\NewTariffPaymentDTO;
use App\Models\CentralUser;
use App\Service\Payments\Core\ConfirmationResponse;
use App\Service\Payments\Core\HasIdempotenceKey;
use App\Service\Payments\Core\HasPriceConverter;
use App\Service\Payments\Core\Hmac;
use App\Service\Payments\Core\PaymentConnector;
use Exception;
use GuzzleHttp\Promise\PromiseInterface;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class ProdamusConnector implements PaymentConnector
{
    use HasIdempotenceKey;
    use HasPriceConverter;

    public function __construct(
        private readonly string $shopUrl,
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
    public function pay(NewTariffPaymentDTO $data, CentralUser $user): ConfirmationResponse
    {
        $price = $this->getPrice($data);
        $paymentId = $this->generateIdempotenceKey();
        $data = [
            'do' => 'link',
            'type' => 'json',
            'demo_mode' => 0,
            'callbackType' => 'json',
            'installments_disabled' => 1,
            'order_id' => $paymentId,
            'customer_phone' => $user->phone,
            'currency' => $data->currency,
            'products' => [
                [
                    'sku' => $paymentId,
                    'name' => 'Оплата тарифа',
                    'price' => (string)$price->getTotal(),
                    'quantity' => '1'
                ]
            ]
        ];

        $signature = new Hmac($data, $this->shopKey);
        $data['signature'] = $signature->create();

        $response = $this->submit($data);

        // Check the status of the payment
        if (!$response->successful()) {
            throw new Exception($response->reason());
        }

        $resp = json_decode($response->body(), true);
        return new ConfirmationResponse(
            $resp['payment_link'],
            $paymentId,
            [],
            $response->successful()
        );
    }

    private function submit(array $data): PromiseInterface|Response
    {
        return Http::withHeaders([
            'Content-type' => 'text/plain',
            'charset' => 'utf-8'
        ])->get($this->shopUrl, $data);
    }

    public function getShopKey(): string
    {
        return $this->shopKey;
    }
}