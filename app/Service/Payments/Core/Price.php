<?php

namespace App\Service\Payments\Core;

class Price
{
    public function __construct(
        public string $value,
        public float  $currency
    )
    {
    }
}