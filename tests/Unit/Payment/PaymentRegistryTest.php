<?php

namespace Tests\Unit\Payment;

use Illuminate\Foundation\Testing\TestCase;
use Tests\CreatesApplication;

class PaymentRegistryTest extends TestCase
{
    use CreatesApplication;

    public function test_it_can_register_correct_transaction_payment()
    {
        dd(1);
//        dd(1);
//        $provider = Gateway::get('rub');
//        dd($provider);
//        $provider->pay();
    }
}
