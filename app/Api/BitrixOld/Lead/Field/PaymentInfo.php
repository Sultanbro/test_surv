<?php

namespace App\Api\BitrixOld\Lead\Field;

use App\Api\BitrixOld\Lead\Field;
use App\Models\Tariff\TariffPayment;


final class PaymentInfo extends Field
{
    public function __construct(
        TariffPayment $payment,
        ?string $tenantId,
    )
    {
        $portal = $tenantId || 'Jobtron.org';

        $strPayment = "Платеж: $payment->id"
            .'\n'."Портал: $portal"
            .'\n'."Статус: $payment->status"
            .'\n'."Платежный сервис: $payment->service_for_payment"
            .'\n'."Внешний ID: $payment->payment_id";

        parent::__construct('UF_CRM_1677471730901', $strPayment);
    }
}
