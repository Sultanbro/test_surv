<?php

namespace App\Service\Referral\Core;

use App\User;
use Carbon\Carbon;

interface TransactionInterface
{
    public function touch(User $referral, PaidType $type);

    public function useDate(Carbon $date);
}