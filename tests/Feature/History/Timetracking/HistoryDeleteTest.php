<?php

namespace Tests\Feature\History\Timetracking;

use App\Fine;
use App\Salary;
use App\TimetrackingHistory;
use App\User;
use Tests\TenantTestCase;

class HistoryDeleteTest extends TenantTestCase
{

    public function test_it_can_delete_bonus()
    {
        $amount = 500;
        $type = 'bonus';
        $realType = 'bonus';
        $comment = 'some comment';
        $user = User::factory()->create();
        $salary = Salary::factory()->create([
            'user_id' => $user->id,
            'bonus' => $amount,
            'comment_bonus' => $amount
        ]);
        $this->assertDatabaseHas('salaries', [
            'id' => $salary->id,
            'bonus' => 500,
        ]);
        $params = [
            'reason' => 'sebaceous you idiot'
        ];

        $history = TimetrackingHistory::query()->create([
            'user_id' => $user->id,
            'author_id' => $user->id,
            'author' => $user->last_name . ' ' . $user->name,
            'date' => now(),
            'description' => 'Добавлен <b>' . $type . '</b> на сумму ' . $amount . '<br> Комментарии: ' . $comment,
            'payload' => json_encode([
                'type' => $realType,
                'amount' => $amount,
                'salary_id' => $salary->getKey()
            ])
        ]);

        $response = $this->json('delete', "timetracking/salaries/histories/$history->id", $params);
        $response->assertStatus(200);
        $this->assertDatabaseHas('salaries', [
            'id' => $salary->id,
            'bonus' => 0,
        ]);
    }

    public function test_it_can_delete_paid()
    {
        $amount = 500;
        $type = 'paid';
        $realType = 'paid';
        $comment = 'some comment';
        $user = User::factory()->create();
        $salary = Salary::factory()->create([
            'user_id' => $user->id,
            'paid' => $amount,
            'comment_paid' => $amount
        ]);

        $this->assertDatabaseHas('salaries', [
            'id' => $salary->id,
            'paid' => 500,
        ]);

        $params = [
            'reason' => 'sebaceous you idiot'
        ];

        $history = TimetrackingHistory::query()->create([
            'user_id' => $user->id,
            'author_id' => $user->id,
            'author' => $user->last_name . ' ' . $user->name,
            'date' => now(),
            'description' => 'Добавлен <b>' . $type . '</b> на сумму ' . $amount . '<br> Комментарии: ' . $comment,
            'payload' => json_encode([
                'type' => $realType,
                'amount' => $amount,
                'salary_id' => $salary->getKey()
            ])
        ]);

        $response = $this->json('delete', "timetracking/salaries/histories/$history->id", $params);
        $response->assertStatus(200);
        $this->assertDatabaseHas('salaries', [
            'id' => $salary->id,
            'paid' => 0,
        ]);
    }

    public function test_it_can_delete_fine()
    {
        $amount = 500;
        $type = 'fine';
        $realType = 'fine';

        $comment = 'some comment';
        $user = User::factory()->create();
        $fine = Fine::factory()->create();
        $user->fines()->attach($fine, [
            'day' => now()->format("Y-m-d")
        ]);

        $this->assertDatabaseHas('user_fines', [
            'user_id' => $user->id,
            'fine_id' => $fine->id,
        ]);

        $params = [
            'reason' => 'sebaceous you idiot'
        ];

        $history = TimetrackingHistory::query()->create([
            'user_id' => $user->id,
            'author_id' => $user->id,
            'author' => $user->last_name . ' ' . $user->name,
            'date' => now(),
            'description' => 'Добавлен <b>' . $type . '</b> на сумму ' . $amount . '<br> Комментарии: ' . $comment,
            'payload' => json_encode([
                'type' => $realType,
                'fine_id' => $fine->getKey(),
                'user_id' => $user->getKey(),
                'day' => now(),
            ])
        ]);

        $response = $this->json('delete', "timetracking/salaries/histories/$history->id", $params);
        $response->assertStatus(200);
        $this->assertDatabaseMissing('user_fines', [
            'user_id' => $user->id,
            'fine_id' => $fine->id
        ]);
    }

    protected function setUp(): void
    {
        parent::setUp();
        $authUser = User::factory()->create();
        $this->actingAs($authUser);
    }
}
