<?php

namespace App\Service\Payment\Prodamus;

use App\Service\Payment\Core\Webhook\BaseWebhookMapper;
use App\Service\Payment\Core\Webhook\HasState;
use Illuminate\Support\Str;

class ProdamusWebhookMapper implements BaseWebhookMapper
{
    use HasState;

    private string|int $transactionId;
    private bool $success;
    private array $payload = [];

    public function map(array $data): void
    {
        $this->transactionId = $data['fields']['order_id'];
        $this->success = Str::lower($data['fields']['payment_status'] ?? 'failed') === 'success';
        $this->payload = $data['fields'];
    }
}