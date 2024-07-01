<?php

namespace App\Service\Payment\WalletOne;

use App\Service\Payment\Core\Webhook\BaseWebhookMapper;
use App\Service\Payment\Core\Webhook\HasState;
use Illuminate\Support\Str;

class WalletOneWebhookMapper implements BaseWebhookMapper
{
    use HasState;

    private mixed $transactionId;
    private bool $success;

    public function map(array $data): void
    {
        $this->transactionId = $data['WMI_PAYMENT_NO'];
        $this->success = Str::lower($data['WMI_ORDER_STATE']) == "accepted";
    }
}