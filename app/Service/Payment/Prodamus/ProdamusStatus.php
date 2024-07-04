<?php

namespace App\Service\Payment\Prodamus;

use App\Models\Invoice;
use App\Service\Payment\Core\Status\BasePaymentStatus;

class ProdamusStatus implements BasePaymentStatus
{
    public function __construct(private readonly ProdamusConnector $connector)
    {
    }

    public function getStatus(Invoice $payment): string
    {
    }
}