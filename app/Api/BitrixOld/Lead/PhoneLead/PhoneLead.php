<?php

namespace App\Api\BitrixOld\Lead\PhoneLead;

use App\Api\BitrixOld;
use App\Api\BitrixOld\Lead\Field\AssignedToAlina as AssignedToAlinaField;
use App\Api\BitrixOld\Lead\Field\Field;
use App\Api\BitrixOld\Lead\Field\Phone as PhoneField;
use App\Api\BitrixOld\Lead\Fields;
use App\Api\BitrixOld\Lead\Lead;

final class PhoneLead extends Lead
{

    public function __construct(
        Data       $data,
        ?BitrixOld $bitrix,
    )
    {
        parent::__construct(new Fields(
            new Field('TITLE', "Jobtron.org - " . $data->name . ' - ' . $data->phone),
            new Field('NAME', $data->name),
            new Field("UF_CRM_1689140803", $data->name),
            new PhoneField($data->phone),
            new AssignedToAlinaField(),
        ), $bitrix);
    }
}
