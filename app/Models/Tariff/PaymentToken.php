<?php

namespace App\Models\Tariff;

class PaymentToken
{
    public function __construct(public readonly string $token)
    {
    }
}