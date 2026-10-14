<?php

namespace App\Api\BitrixOld\Lead;

use App\Api\BitrixOld;
use App\Api\BitrixOld\Lead\Field\AssignedToAlina as AssignedToAlinaField;
use App\Api\BitrixOld\Lead\Field\Field;
use App\Api\BitrixOld\Lead\Field\Phone as PhoneField;
use App\Api\BitrixOld\Lead\Field\InvoiceInfo;
use App\Models\CentralUser;
use App\Models\Invoice;

final class PracticumInvoiceLead extends Lead
{

    public function __construct(
        CentralUser $user,
        Invoice     $invoice,
        ?string     $tenantId,
        ?BitrixOld  $bitrix,
    )
    {
        parent::__construct(new Fields(
            new Field('TITLE', "Jobtron.org - Платеж практикум: $invoice->id"),
            new Field('NAME', $user->name),
            new Field("UF_CRM_1689140803", $user->name),
            new Field("STAGE_ID", "C38:NEW"),
            new Field("TYPE_ID", "SALE"),
            new Field("OPPORTUNITY", 1000),
            new Field("CURRENCY_ID", "kzt"),
            new Field("CATEGORY_ID", "38"),
            new InvoiceInfo($invoice, $tenantId),
            new AssignedToAlinaField(),
        ), $bitrix);

        if ($user->phone) {
            $this->fields->addFields(new PhoneField($user->phone));
        }
    }
}
