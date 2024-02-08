<?php

namespace App\Service\Salary;

use App\Repositories\UserRepository;
use App\Salary;
use App\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class UpdateSalaryServiceBetweenRange implements UpdateSalaryInterface
{
    public function __construct(
        private readonly UserRepository $userRepository
    )
    {
    }

    public function touch(string $date, int $groupId = null): void
    {
        [$startDate, $endDate] = $this->diapason($date);

        // Get all users within the date range using whereBetween
        /** @var Collection<User> $users */
        $users = $this->userRepository->betweenDate($startDate, $endDate)
            ->when($groupId, function (Builder $query) use ($groupId) {
                $query->whereHas('groups', function (Builder $query) use ($groupId) {
                    $query->where('id', $groupId);
                    $query->where('status', 'active');
                });
            })
            ->get();

        while ($startDate <= $endDate) {
            $this->updateDaySalary($users, $startDate);
            $startDate->addDay();
        }
    }

    private function diapason(string $date): array
    {
        $date = Carbon::parse($date);
        return [
            Carbon::parse($date)->startOfMonth(),
            Carbon::parse($date)->endOfMonth()
        ];
    }

    private function updateDaySalary(Collection $users, Carbon $date): void
    {
        foreach ($users as $user) {

            $amount = $this->getUserRate($user);

            dd_if($user['id'] == 5, $this->isWorked($date, $user));

            if (!$this->isWorked($date, $user)) continue;

            // Find the salary for the user
            $salary = $this->getSalary($user, $date);

            // Find the rate for the user

            if ($salary && (int)$salary->amount === 0) {
                $salary->update([
                    'amount' => $amount,
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
                    'amount' => $amount
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

    private function getSalary(User $user, Carbon $date): ?Salary
    {
        /** @var null|Salary */
        return $user->salaries()
            ->whereYear('date', $date->year)
            ->whereMonth('date', $date->month)
            ->whereDay('date', $date->day)
            ->first();
    }

    private function getUserRate(mixed $user): int
    {
        return $user->zarplata?->zarplata ?? 70000;
    }
}