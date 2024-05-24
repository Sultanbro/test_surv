<?php
declare(strict_types=1);

namespace App\Service\Payment\Core\Base;

use App\DTO\Payment\CreateInvoiceDTO;
use App\Service\Payment\Core\Webhook\Invoice;
use App\Service\Payment\Core\Customer\CustomerDto;

interface PaymentConnector
{
    public function newInvoice(CreateInvoiceDTO $invoice, CustomerDto $customer): Invoice;

    public function getShopKey(): string;
}