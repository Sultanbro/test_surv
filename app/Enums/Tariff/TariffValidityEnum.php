<?php

namespace App\Enums\Tariff;

enum TariffValidityEnum: string
{
    use BaseEnum;

    case Monthly = 'monthly';
//    case Yearly = 'yearly';
    case Annual = 'annual';
}