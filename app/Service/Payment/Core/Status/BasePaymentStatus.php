<?php

namespace App\Service\Payment\Core\Status;

use App\Models\Invoice;

interface BasePaymentStatus
{
    public function getStatus(Invoice $payment): string;
}