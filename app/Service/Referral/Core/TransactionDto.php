<?php

namespace App\Service\Referral\Core;

use App\User;
use Carbon\Carbon;

class TransactionDto
{

    /**
     * @param User|null $user
     * @param float|null $salary
     * @param Carbon|null $date
     */
    public function __construct(
        public null|User     $user = null
        , public null|float  $salary = null
        , public null|Carbon $date = null
    )
    {
    }
}