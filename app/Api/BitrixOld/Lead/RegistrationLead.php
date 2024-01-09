<?php

namespace App\Api\BitrixOld\Lead;

use App\Api\BitrixOld;
use App\Api\BitrixOld\Lead\Field\AssignedToAlina as AssignedToAlinaField;
use App\Api\BitrixOld\Lead\Field\Field;
use App\Api\BitrixOld\Lead\Field\Phone as PhoneField;
use App\User;

final class RegistrationLead extends Lead
{

    public function __construct(
        User       $user,
        ?BitrixOld $bitrix,
    )
    {
        parent::__construct(new Fields(
            new Field('TITLE', "Jobtron.org - Регистрация " . $user->name),
            new Field('NAME', $user->name),
            new Field("UF_CRM_1689140803", translit($user->name)),
            new AssignedToAlinaField(),
        ), $bitrix);

        if ($user->phone) {
            $this->fields->addFields(new PhoneField($user->phone));
        }
    }
}
