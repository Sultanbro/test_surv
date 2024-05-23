<?php

namespace App\Enums\Tariff;

enum TariffValidityEnum: string
{
    use BaseEnum;

    case Monthly = 'monthly';
    case Monthly_3 = '3_monthly';
    case Yearly = 'yearly';
    case Annual = 'annual';
}