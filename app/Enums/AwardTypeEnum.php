<?php

namespace App\Enums;

use App\Enums\Abstr\DictionaryEnum;
use App\Position;
use App\ProfileGroup;
use App\User;

class AwardTypeEnum
{
    const NOMINATION = 1;
    const CERTIFICATE = 2;
    const ACCRUAL = 3;

    const TYPES = [
        'nomination' => self::NOMINATION,
        'certificate' => self::CERTIFICATE,
        'accrual' => self::ACCRUAL,
    ];
    const VALUES = [
        self::NOMINATION => 'nomination',
        self::CERTIFICATE => 'certificate',
        self::ACCRUAL => 'accrual',
    ];
}
