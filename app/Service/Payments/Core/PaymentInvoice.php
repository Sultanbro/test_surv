<?php

namespace App\Service\Payments\Core;

abstract class PaymentInvoice
{
    abstract public function handle(): InvoiceResponse;
}