<?php

namespace Tests\Unit\Service\Referral;

use App\Models\Referral\Referrer;
use App\Service\Referral\ReferrerSalaryHandler;
use Tests\TenantTestCase;

class ReferrerSalaryHandlerTest extends TenantTestCase
{

    public function test_can_transfer_salaries_by_transaction(): void
    {
        $referrer1 = Referrer::factory()->create();
        $referrer2 = Referrer::factory()->create([
            'parent_referrer_id' => $referrer1->id
        ]);
        $referrer3 = Referrer::factory()->create([
            'parent_referrer_id' => $referrer2->id
        ]);


        $handler = $this->app->make(ReferrerSalaryHandler::class);

        $handler->apply($referrer3);

        $this->assertDatabaseHas('salaries', [
            'user_id' => $referrer3->user->id,
            'bonus' => 10000,
        ]);
        $this->assertDatabaseHas('salaries', [
            'user_id' => $referrer2->user->id,
            'bonus' => 5000,
        ]);
        $this->assertDatabaseHas('salaries', [
            'user_id' => $referrer1->user->id,
            'bonus' => 2000,
        ]);
    }
}
