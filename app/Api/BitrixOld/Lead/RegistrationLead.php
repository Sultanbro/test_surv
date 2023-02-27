<?php

namespace App\Api\BitrixOld\Lead;

use App\Api\BitrixOld;
use App\Api\BitrixOld\Lead\Field\AssignedToValeria as AssignedToValeriaField;
use App\Api\BitrixOld\Lead\Field\Field;
use App\Api\BitrixOld\Lead\Field\Phone as PhoneField;
use App\Api\BitrixOld\Lead\Fields;
use App\Api\BitrixOld\Lead\Lead;
use App\User;

final class RegistrationLead extends Lead
{

    public function __construct(
        User $user,
        ?BitrixOld $bitrix,
    )
    {
        parent::__construct(new Fields(
            new Field('TITLE', "Jobtron.org - Регистрация " . $user->name),
            new Field('NAME', $user->name),
            new AssignedToValeriaField(),
        ), $bitrix);

        if ($user->phone) {
            $this->fields->addFields(new PhoneField($user->phone));
        }
    }
}
