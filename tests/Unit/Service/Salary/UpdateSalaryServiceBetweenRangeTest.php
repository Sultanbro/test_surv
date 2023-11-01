<?php

namespace Tests\Unit\Service\Salary;

use App\Service\Salary\UpdateSalaryInterface;
use App\User;
use Illuminate\Support\Facades\DB;
use Tests\TenantTestCase;
use Throwable;

class UpdateSalaryServiceBetweenRangeTest extends TenantTestCase
{
    /**
     * @throws Throwable
     */
    public function test_it_can_update_salary_between_range()
    {
        DB::beginTransaction();
        $users = User::factory(15)
            ->create()
            ->map(function (User $user) {
                $user->description()->create([
                    'is_trainee' => false,
                    'applied' => now()->format("Y-m-d")
                ]);
                $user->zarplata()->create([
                    'zarplata' => 100000,
                    'card_number' => 54545454545,
                    'jysan' => 54,
                    'card_jysan' => 454,
                    'jysan_cardholder' => 'Vahagn'
                ]);
                return $user;
            });

        $service = app(UpdateSalaryInterface::class);
        $service->touch();
        $this->assertDatabaseCount('salaries', 15 * 10);
        $days = [1, 2, 3, 4, 5, 6, 7, 8, 9];
        foreach ($days as $day) {
            $date = now()->subDays($day)->format("Y-m-d");
            $this->assertDatabaseHas('salaries', [
                'date' => $date
            ]);
        }
        foreach ($users as $user) {
            $this->assertDatabaseHas('salaries', [
                'user_id' => $user->getKey()
            ]);
        }
        DB::rollBack();
    }

    /**
     * @throws Throwable
     */
    public function test_it_can_skip_salary_between_range_when_already_updated()
    {
        DB::beginTransaction();
        $users = User::factory(5)
            ->create()
            ->map(function (User $user) {
                $user->description()->create([
                    'is_trainee' => false,
                    'applied' => now()->format("Y-m-d")
                ]);
                $user->zarplata()->create([
                    'zarplata' => 100000,
                    'card_number' => 54545454545,
                    'jysan' => 54,
                    'card_jysan' => 454,
                    'jysan_cardholder' => 'Vahagn'
                ]);
                $user->salaries()->create([
                    'date' => now()->format("Y-m-d"),
                    'note' => 'this is default',
                    'paid' => 0,
                    'bonus' => 0,
                    'comment_paid' => '',
                    'comment_bonus' => '',
                    'comment_award' => '',
                    'amount' => 3000
                ]);
                $user->salaries()->create([
                    'date' => now()->subDay()->format("Y-m-d"),
                    'note' => 'this is default',
                    'paid' => 0,
                    'bonus' => 0,
                    'comment_paid' => '',
                    'comment_bonus' => '',
                    'comment_award' => '',
                    'amount' => 3000
                ]);
                return $user;
            });
        $service = app(UpdateSalaryInterface::class);
        $service->touch();
        $this->assertDatabaseCount('salaries', 5 * 10);
        $days = [1, 2, 3, 4, 5, 6, 7, 8, 9];
        foreach ($days as $day) {
            $date = now()->subDays($day)->format("Y-m-d");
            $this->assertDatabaseHas('salaries', [
                'date' => $date
            ]);
        }
        foreach ($users as $user) {
            $this->assertDatabaseHas('salaries', [
                'user_id' => $user->getKey()
            ]);
        }
        DB::rollBack();
    }
}
