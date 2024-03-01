<?php

namespace Tests\Feature\History\Timetracking;

use App\Salary;
use App\User;
use Tests\TenantTestCase;

class HistoryRestoreTest extends TenantTestCase
{

    public function test_it_can_restore_bonus()
    {
        $user = User::factory()->create();
        $salary = Salary::factory()->create([
            'user_id' => $user->id
        ]);


        $response = $this->json('get', 'referrals/url');
    }

    protected function setUp(): void
    {
        parent::setUp();
        $authUser = User::factory()->create();
        $this->actingAs($authUser);
    }
}
