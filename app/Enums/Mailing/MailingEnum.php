<?php

namespace App\Enums\Mailing;

enum MailingEnum
{
    const WEEKLY    = 'weekly';
    const MONTHLY   = 'monthly';
    const DAILY     = 'daily';

    const GROUP     = 'App\ProfileGroup';
    const USER      = 'App\User';
    const POSITION  = 'App\Position';

    const TYPES = [
        'App\ProfileGroup'  => 'group',
        'App\User'          => 'individual',
        'App\Position'      => 'position'
    ];
}
