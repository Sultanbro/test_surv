<?php

namespace App\Service\Payments\Core;

abstract class PaymentReport
{
    abstract public function handle(): InvoiceResponse;
}