<?php

namespace App\Enums;

enum SalaryResourceType: string
{
    const BONUS = 'bonus';
    const AWARD = 'award';
    const PAID = 'paid';
    const REFERRAL = 'referral';
}
