<?php

namespace App\Service\Referral\Core;

enum PaidType:int
{
    case TRAINEE = 1;
    case WORK = 2;
    case ATTESTATION = 3;
}