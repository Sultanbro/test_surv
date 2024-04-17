<?php

namespace Tests\Feature\History\Timetracking;

use App\Fine;
use App\User;
use Tests\TenantTestCase;

class HistoryRestoreTest extends TenantTestCase
{

    public function test_it_can_restore_fine()
    {
        $user = User::factory()->create();
        $fine = Fine::factory()->create();
        $user->fines()->attach($fine, [
            'day' => now()->format("Y-m-d"),
            'deleted_at' => now()->toDateString()
        ]);

        $this->assertDatabaseHas('user_fines', [
            'user_id' => $user->id,
            'fine_id' => $fine->id,
            'deleted_at' => now()->toDateString(),
        ]);

        $params = [
            'reason' => 'because you idiot'
        ];

        $response = $this->json('post', "timetracking/salaries/fines/histories/$user->id/$fine->id", $params);
        $response->assertStatus(200);
        $this->assertDatabaseMissing('user_fines', [
            'user_id' => $user->id,
            'fine_id' => $fine->id,
            'deleted_at' => null
        ]);
    }

    protected function setUp(): void
    {
        parent::setUp();
        $authUser = User::factory()->create();
        $this->actingAs($authUser);
    }
}
