<?php

namespace App\Classes\Enums;

enum TariffKindEnum: string
{
    use BaseEnum;
    case Free     = 'free';
    case Base     = 'base';
    case Standard = 'standard';
    case Pro      = 'pro';
}