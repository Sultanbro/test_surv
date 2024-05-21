<?php

namespace App\Service\Payment\Core;

class Price
{
    public function __construct(
        public string $value,
        public float  $currency
    )
    {
    }
}