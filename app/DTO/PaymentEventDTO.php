<?php

namespace App\DTO;

use App\Enums\Invoice\InvoiceType;

class PaymentEventDTO
{
    public function __construct(
        public int         $id,
        public bool        $successStatus,
        public string      $provider,
        public InvoiceType $eventType
    )
    {
    }
}