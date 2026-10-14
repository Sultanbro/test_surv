<?php

namespace App\Listeners\Payment\Practicum;

use App\DTO\PaymentEventDTO;
use App\Models\Invoice;
use App\Service\Invoice\UpdateDealInBitrix;

class UpdateLeadStatus
{
    public function handle($event): void
    {
        /** @var PaymentEventDTO $dto */
        $dto = $event->dto;

        if (!$dto->successStatus) return;
        $invoice = Invoice::getByPayerPhone($dto->id);

        $updateLeadService = new UpdateDealInBitrix($invoice);
        $updateLeadService->send();
    }
}
