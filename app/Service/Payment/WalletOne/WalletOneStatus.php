<?php

namespace App\Service\Payment\WalletOne;

use App\Models\Invoice;
use App\Service\Payment\Core\Status\BasePaymentStatus;

class WalletOneStatus implements BasePaymentStatus
{

    public function getStatus(Invoice $payment): string
    {
        return '';
    }
}