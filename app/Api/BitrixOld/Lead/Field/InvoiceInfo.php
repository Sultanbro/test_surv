<?php

namespace App\Api\BitrixOld\Lead\Field;

use App\Models\Invoice;

final class InvoiceInfo extends Field
{
    public function __construct(
        Invoice $payment,
        ?string $tenantId,
    )
    {
        $portal = $tenantId || 'Jobtron.org';

        $strPayment = "Платеж практикум: $payment->id"
            . '\n' . "Портал: $portal"
            . '\n' . "Статус: $payment->status"
            . '\n' . "Платежный сервис: $payment->provider"
            . '\n' . "Внешний ID: $payment->transaction_id";

        parent::__construct('UF_CRM_1677471730901', $strPayment);
    }
}
