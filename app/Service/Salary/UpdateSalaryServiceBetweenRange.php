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

    public function touch(string $date): void
    {
        [$endDate, $startDate] = $this->diapason($date);

        // Get all users within the date range using whereBetween
        /** @var Collection<User> $users */
        $users = $this->userRepository->betweenDate($startDate, $endDate);

        while ($startDate <= $endDate) {
            $this->updateDaySalary($users, $startDate);
            $startDate->addDay();
        }
    }

    private function diapason(string $date): array
    {
        $date = Carbon::parse($date);
        return [
            $endDate = $date,
            $endDate->copy()->subDays(9)
        ];
    }

    private function updateDaySalary(Collection $users, Carbon $date): void
    {
        foreach ($users as $user) {

            if (!$this->isWorked($date, $user)) continue;

            // Find the salary for the user
            $salary = $user->salaries()
                ->whereDate('date', $date->format("Y-m-d"))
                ->first();

            // Find the zarplata for the user
            $zarplata = $user->zarplata;

            $salary_amount = $zarplata ? $zarplata->zarplata : 70000;

            if ($salary && (int)$salary->amount === 0) {
                $salary->update([
                    'date' => $date,
                    'note' => 'test',
                    'paid' => 0,
                    'bonus' => 0,
                    'comment_paid' => '',
                    'comment_bonus' => '',
                    'comment_award' => '',
                    'amount' => $salary_amount,
                ]);
            }
            if (!$salary) {
                $user->salaries()->create([
                    'date' => $date,
                    'note' => 'test',
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

    private function isWorked(Carbon $date, User $user): bool
    {
        return $user->timetracking()
            ->whereYear('enter', $date->year)
            ->whereMonth('enter', $date->month)
            ->whereDay('enter', $date->day)
            ->exists();
    }
}