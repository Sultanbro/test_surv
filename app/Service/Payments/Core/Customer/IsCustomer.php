<?php

namespace App\Service\Payments\Core\Customer;

interface IsCustomer
{
    public function asCustomer(): CustomerDto;
}