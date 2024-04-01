<?php

namespace Tests\Feature\Payment;

use App\DTO\Api\PaymentDTO;
use App\Enums\Payments\PaymentStatusEnum;
use App\Enums\Tariff\TariffKindEnum;
use App\Models\CentralUser;
use App\Models\Tariff\Tariff;
use App\Models\Tariff\TariffPayment;
use App\Service\Payments\Core\PaymentFactory;
use Exception;
use Tests\TenantTestCase;
use Throwable;

class PayByProdamusTest extends TenantTestCase
{
    /**
     * @throws Exception
     * @throws Throwable
     */
    public function test_user_can_pay_tariff_plan()
    {
        $user = CentralUser::factory()->create();
        $tariff = Tariff::query()->where('kind', TariffKindEnum::Base)->first();
        $data = new PaymentDTO(
            'RUB',
            $tariff->getKey(),
            20,
            'prodamus'
        );
        $factory = new PaymentFactory;
        $provider = $factory->currencyProvider('rub');
        $response = $provider->pay($data, $user);
        $this->assertDatabaseHas('tariff_payment', [
            'payment_id' => $response->getPaymentId()
        ], 'mysql');
    }

    /**
     * @throws Exception
     */
    public function test_user_can_transaction_status()
    {
        $user = CentralUser::factory()->create();
        $tariff = Tariff::query()->where('kind', TariffKindEnum::Pro)->first();
        $data = new PaymentDTO(
            'RUB',
            $tariff->getKey(),
            20,
            'prodamus'
        );
        $factory = new PaymentFactory;
        $provider = $factory->currencyProvider('rub');
        $response = $provider->pay($data, $user);
        $response = $provider->info($response->getPaymentId())->getPaymentStatus();
        $this->assertEquals(PaymentStatusEnum::STATUS_SUCCESS, $response);
    }

    /**
     * @throws Exception
     */
    public function test_user_can_update_transaction_status()
    {
        $user = CentralUser::factory()->create();
        $tariff = Tariff::query()->where('kind', TariffKindEnum::Pro)->first();
        $data = new PaymentDTO(
            'RUB',
            $tariff->getKey(),
            20,
            'prodamus'
        );

        $factory = new PaymentFactory;
        $provider = $factory->currencyProvider('rub');
        $response = $provider->pay($data, $user);

        /** @var TariffPayment $transaction */
        $transaction = TariffPayment::query()->where('payment_id', $response->getPaymentId())->first();
        $provider->updateStatusByPayment($transaction);
        $updatedStatus = $provider->info($response->getPaymentId())->getPaymentStatus();

        $this->assertDatabaseHas('tariff_payment', [
            'payment_id' => $response->getPaymentId(),
            'status' => $updatedStatus
        ], 'mysql');
    }
}
