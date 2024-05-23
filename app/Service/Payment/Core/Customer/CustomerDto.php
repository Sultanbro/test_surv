<?php

namespace App\Service\Payment\Core\Customer;

class CustomerDto
{

    public function __construct(
        public int     $id,
        public string  $currency,
        public string  $name,
        public ?string $phone = null,
        public ?string $email = null
    )
    {
    }
}