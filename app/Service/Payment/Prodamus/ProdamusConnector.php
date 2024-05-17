<?php
declare(strict_types=1);

namespace App\Service\Payment\Prodamus;

use App\DTO\Payment\CreateInvoiceDTO;
use App\Service\Payment\Core\Customer\CustomerDto;
use App\Service\Payment\Core\Invoice;
use App\Service\Payment\Core\HasIdempotenceKey;
use App\Service\Payment\Core\Hmac;
use App\Service\Payment\Core\PaymentConnector;
use Exception;
use GuzzleHttp\Promise\PromiseInterface;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class ProdamusConnector implements PaymentConnector
{
    use HasIdempotenceKey;

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
    public function createNewInvoice(CreateInvoiceDTO $invoice, CustomerDto $customer): Invoice
    {
        $paymentId = $this->generateIdempotenceKey();
        $params = [
            'do' => 'link',
            'type' => 'json',
            'demo_mode' => 0,
            'callbackType' => 'json',
            'installments_disabled' => 1,
            'order_id' => $paymentId,
            'customer_phone' => $customer->phone,
            'currency' => $invoice->currency,
            'products' => [
                [
                    'sku' => $paymentId,
                    'name' => $invoice->description,
                    'price' => $invoice->price,
                    'quantity' => (string)$invoice->quantity
                ]
            ]
        ];

        $signature = new Hmac($params, $this->shopKey);
        $params['signature'] = $signature->create();

        $response = $this->submit($params);

        // Check the status of the payment
        if (!$response->successful()) {
            throw new Exception($response->reason());
        }

        $resp = json_decode($response->body(), true);

        return new Invoice(
            url: $resp['payment_link'],
            paymentId: $paymentId,
            success: $response->successful()
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