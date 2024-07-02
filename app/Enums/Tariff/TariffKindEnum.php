<?php

namespace App\Enums\Tariff;

enum TariffKindEnum: string
{
    use BaseEnum;
    case Free     = 'free';
    case Base     = 'base';
    case Standard = 'standard';
    case Pro      = 'pro';
}