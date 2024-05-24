<?php
declare(strict_types=1);

namespace App\Service\Payment\Core;

use App\DTO\Payment\CreateInvoiceDTO;
use App\Service\Payment\Core\Callback\Invoice;
use App\Service\Payment\Core\Customer\CustomerDto;

interface PaymentConnector
{
    public function createNewInvoice(CreateInvoiceDTO $invoice, CustomerDto $customer): Invoice;

    public function getShopKey(): string;
}