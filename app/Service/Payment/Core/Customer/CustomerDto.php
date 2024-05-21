<?php

namespace App\Service\Payment\Core\Customer;

class CustomerDto
{
    public function __construct(
        public int    $id,
        public string $name,
        public string $phone,
        public string $currency
    )
    {
    }
}