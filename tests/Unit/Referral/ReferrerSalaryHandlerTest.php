<?php

namespace Tests\Unit\Referral;

use App\Service\Referral\SalaryHandler;
use App\User;
use Tests\TenantTestCase;

class ReferrerSalaryHandlerTest extends TenantTestCase
{
    public function test_can_transfer_salaries_by_transaction(): void
    {
        $referrer1 = User::factory()->create();
        $referrer2 = User::factory()->create([
            'referrer_id' => $referrer1->id
        ]);
        $referrer3 = User::factory()->create([
            'referrer_id' => $referrer2->id
        ]);


        $handler = $this->app->make(SalaryHandler::class);

        $handler->handle($referrer3);

        $this->assertDatabaseHas('salaries', [
            'user_id' => $referrer3->id,
            'award' => 10000,
        ]);
        $this->assertDatabaseHas('salaries', [
            'user_id' => $referrer2->id,
            'award' => 5000,
        ]);
        $this->assertDatabaseHas('salaries', [
            'user_id' => $referrer1->id,
            'award' => 2000,
        ]);
    }
}
