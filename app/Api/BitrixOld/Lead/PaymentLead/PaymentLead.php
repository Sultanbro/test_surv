<?php

namespace App\Api\BitrixOld;

use App\Api\BitrixOld;
use App\Api\BitrixOld\Lead\Field;
use App\Api\BitrixOld\Lead\Field\AssignedToValeria as AssignedToValeriaField;
use App\Api\BitrixOld\Lead\Field\Phone as PhoneField;
use App\Api\BitrixOld\Lead\Field\PaymentInfo as PaymentInfoField;
use App\Api\BitrixOld\Lead\Fields;
use App\Models\Tariff\TariffPayment;
use App\User;

final class PaymentLead extends Lead
{

    public function __construct(
        User $user,
        TariffPayment $payment,
        ?string $tenantId,
        ?BitrixOld $bitrix,
    )
    {
        parent::__construct(new Fields(
            new Field('TITLE', "Jobtron.org - Платеж: $payment->id"),
            new Field('NAME', $user->name),
            new PaymentInfoField($payment, $tenantId),
            new AssignedToValeriaField(),
        ), $bitrix);

        if ($user->phone) {
            $this->fields->addFields(new PhoneField($user->phone));
        }
    }
}
