<?php
declare(strict_types=1);

namespace App\Service\Payments\Core;

use App\DTO\Payment\CreateInvoiceDTO;
use App\Models\CentralUser;

interface PaymentConnector
{
    public function createNewInvoice(CreateInvoiceDTO $data, CentralUser $user): Invoice;

    public function getShopKey(): string;
}