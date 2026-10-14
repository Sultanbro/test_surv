<?php

namespace App\DTO\Payment;

class CreateTrialPaymentSubscriptionDto
{
    public function __construct(
        public string $tenantId
    )
    {
    }
}