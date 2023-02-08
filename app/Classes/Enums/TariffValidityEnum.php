<?php

namespace App\Classes\Enums;

enum TariffValidityEnum: string
{
    use BaseEnum;
    case Monthly = 'monthly';
    case Annual  = 'annual';
}