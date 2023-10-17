<?php

namespace App\Repositories\Referral;

class RequestMock
{
    public function __construct(
        public readonly string $month
    )
    {
    }
}