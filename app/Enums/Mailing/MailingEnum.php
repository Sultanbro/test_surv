<?php

namespace App\Enums\Mailing;

enum MailingEnum
{
    const WEEKLY    = 'weekly';
    const MONTHLY   = 'monthly';

    const GROUP     = 'App\ProfileGroup';
    const USER      = 'App\User';
    const POSITION  = 'App\Position';
}
