<?php

namespace Tests\Unit\Payment;

use App\DTO\Api\NewTariffPaymentDTO;
use App\Models\CentralUser;
use App\Service\Payments\Core\PaymentFactory;
use Exception;
use Tests\TestCase;

class ProdamusServiceTest extends TestCase
{
    /**
     * @throws Exception
     */
    public function test_it_can_create_correct_transaction_payment()
    {
        $user = CentralUser::factory()->create();
        $data = new NewTariffPaymentDTO();
        $factory = new PaymentFactory;
        $provider = $factory->currencyProvider('rub');
        $provider->pay();
    }
}
