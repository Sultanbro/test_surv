<?php

namespace App\Api\BitrixOld\PhoneLead;

use App\Api\BitrixOld;
use App\Api\BitrixOld\Lead\Field\AssignedToValeria as AssignedToValeriaField;
use App\Api\BitrixOld\Lead\Field\Field;
use App\Api\BitrixOld\Lead\Field\Phone as PhoneField;
use App\Api\BitrixOld\Lead\Fields;
use App\Api\BitrixOld\Lead\Lead;
use App\Api\BitrixOld\PhoneLead\Data;

final class PhoneLead extends Lead
{

    public function __construct(
        Data $data,
        ?BitrixOld $bitrix,
    )
    {
        parent::__construct(new Fields(
            new Field('TITLE', "Jobtron.org - " . $data->name . ' - ' . $data->phone),
            new Field('NAME', $data->name),
            new PhoneField($data->phone),
            new AssignedToValeriaField(),
        ), $bitrix);
    }
}
