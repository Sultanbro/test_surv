<?php

namespace App\Service\Salary;

use App\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

class UpdateSalaryServiceBetweenRange implements UpdateSalaryInterface
{
    public function touch(): void
    {
        $endDate = Carbon::now();
        $startDate = $endDate->copy()->subDays(9);

        // Get all users within the date range using whereBetween
        /** @var Collection<User> $users */
        $users = User::query()
            ->withWhereHas('user_description', fn($query) => $query->where('is_trainee', false))
            ->with(['salaries' => fn($query) => $query->whereBetween('date', [
                $startDate->format("Y-m-d"),
                $endDate->format("Y-m-d")
            ])])
            ->with('zarplata')
            ->where(fn($query) => $query
                ->whereNull('deleted_at')
                ->orWhere(fn($query) => $query->whereBetween('deleted_at', [$startDate->format("Y-m-d"), $endDate->format("Y-m-d")]))
            )
            ->get();

        while ($startDate <= $endDate) {

            $dateToString = $startDate->format("Y-m-d");

            foreach ($users as $user) {

                // Find the salary for the user

                $salary = $user->salaries()->whereDate('date', $startDate)->first();

                // Find the zarplata for the user
                $zarplata = $user->zarplata;

                $salary_amount = $zarplata ? $zarplata->zarplata : 70000;

                if (!$salary) {
                    $user->salaries()->create([
                        'date' => $dateToString,
                        'note' => '',
                        'paid' => 0,
                        'bonus' => 0,
                        'comment_paid' => '',
                        'comment_bonus' => '',
                        'comment_award' => '',
                        'amount' => $salary_amount,
                    ]);
                }
            }
            $startDate->addDay();
        }
    }
}