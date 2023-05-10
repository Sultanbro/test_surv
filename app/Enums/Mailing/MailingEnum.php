<?php

namespace App\Enums\Mailing;

enum MailingEnum
{
    const WEEKLY                        = 'weekly';
    const MONTHLY                       = 'monthly';
    const DAILY                         = 'daily';
    const TRIGGER_APPLIED               = 'apply_employee';
    const TRIGGER_FIRED                 = 'fired_employee';
    const TRIGGER_ABSENT_INTERNSHIP     = 'absent_internship';
    const TRIGGER_MANAGER_ASSESSMENT    = 'manager_assessment';
    const TRIGGER_COACH_ASSESSMENT      = 'coach_assessment';

    const GROUP     = 'App\ProfileGroup';
    const USER      = 'App\User';
    const POSITION  = 'App\Position';

    const FREQUENCIES = [
        self::DAILY,
        self::WEEKLY,
        self::MONTHLY,
        self::TRIGGER_APPLIED,
        self::TRIGGER_FIRED,
        self::TRIGGER_ABSENT_INTERNSHIP,
        self::TRIGGER_MANAGER_ASSESSMENT,
        self::TRIGGER_COACH_ASSESSMENT,
    ];

    const TYPES = [
        'App\ProfileGroup'  => 'group',
        'App\User'          => 'individual',
        'App\Position'      => 'position'
    ];
}
