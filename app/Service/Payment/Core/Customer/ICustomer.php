<?php

namespace App\Service\Payment\Core\Customer;

interface ICustomer
{
    public function toCustomerDTO(): CustomerDto;
}