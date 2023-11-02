<?php

namespace App\Service\Salary;

use App\Repositories\UserRepository;
use App\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

class UpdateSalaryServiceBetweenRange implements UpdateSalaryInterface
{
    public function __construct(
        private readonly UserRepository $userRepository
    )
    {
    }

    public function touch(): void
    {
        [$endDate, $startDate] = $this->diapason();

        // Get all users within the date range using whereBetween
        /** @var Collection<User> $users */
        $users = $this->userRepository->betweenDate($startDate, $endDate);

        while ($startDate <= $endDate) {
            $this->updateDaySalary($users, $startDate);
            $startDate->addDay();
        }
    }

    private function diapason(): array
    {
        return [
            $endDate = Carbon::now(),
            $endDate->copy()->subDays(9)
        ];
    }

    private function updateDaySalary(Collection $users, mixed $date): void
    {
        foreach ($users as $user) {

            // Find the salary for the user

            $salary = $user->salaries()->whereDate('date', $date)->first();

            // Find the zarplata for the user
            $zarplata = $user->zarplata;

            $salary_amount = $zarplata ? $zarplata->zarplata : 70000;

            if (!$salary) {
                $user->salaries()->create([
                    'date' => $date,
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
    }
}