<?php
declare(strict_types=1);

namespace App\Service\Payment\Core\Base;

use App\DTO\Payment\CreateInvoiceDTO;
use App\Service\Payment\Core\Customer\CustomerDto;
use App\Service\Payment\Core\Invoice\InvoiceResponse;

interface PaymentConnector
{
    public function newInvoice(CreateInvoiceDTO $invoice, CustomerDto $customer): InvoiceResponse;

    public function getShopKey(): string;
}