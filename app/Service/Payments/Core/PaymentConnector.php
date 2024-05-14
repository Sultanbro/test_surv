<?php
declare(strict_types=1);

namespace App\Service\Payments\Core;

use App\DTO\Payment\CreateInvoiceDTO;
use App\Service\Payments\Core\Customer\CustomerDto;

interface PaymentConnector
{
    public function createNewInvoice(CreateInvoiceDTO $data, CustomerDto $customer): Invoice;

    public function getShopKey(): string;
}