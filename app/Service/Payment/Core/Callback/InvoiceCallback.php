<?php

namespace App\Service\Payment\Core\Callback;

class InvoiceCallback
{
    public function __construct(
        public string $paymentId,
        public string $status,
    )
    {
    }
}