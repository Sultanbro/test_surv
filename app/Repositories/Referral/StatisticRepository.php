<?php

namespace App\Repositories\Referral;

use App\DayType;
use App\Enums\SalaryResourceType;
use App\Models\Bitrix\Lead;
use App\Salary;
use App\Service\Referral\Core\LeadTemplate;
use App\Service\Referral\Core\ReferrerStatus;
use App\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StatisticRepository implements StatisticRepositoryInterface
{
    protected array $filter = [];

    public function getStatistic(array $filter): array
    {
        $this->filter = $filter;
        return [
            'pivot' => $this->getPivot(),
            'referrers' => $this->getReferrersStatistics()
        ];
    }

    protected function getPivot(): array
    {
        $referrers = User::query()
            ->whereHas('referralLeads')
            ->get();

        $usersCount = $referrers->count();

        $salaryTotal = Salary::query()
            ->where('resource', SalaryResourceType::REFERRAL)
            ->get()
            ->sum('award');

        $leads = Lead::query()
            ->where('segment', LeadTemplate::SEGMENT_ID)
            ->count();

        $deals = Lead::query()
            ->where('segment', LeadTemplate::SEGMENT_ID)
            ->whereNotNull('deal_id')
            ->count();

        $piedTotalForMonth = Salary::query()
            ->where('date', '>=', $this->date())
            ->where('resource', SalaryResourceType::REFERRAL)
            ->sum('award');

        // TODO: figure out how to calculate the earned
        $earnedTotalForMonth = 0;
        foreach ($referrers as $item) {
            $earnedTotalForMonth += $this->getUserEarned($item);
        }

        $accepted = $referrers->where('is_trainee', 0)->count();
        return [
            'employee_price' => $accepted ? $salaryTotal / $referrers->where('is_trainee', 0)
                    ->count() : 0,
            'deal_lead_conversion' => $leads ? ($deals / $leads) * 100 : 0,
            'applied_deal_conversion' => $deals ? ($usersCount / $deals) * 100 : 0,
            'earned' => $earnedTotalForMonth,
            'paid' => $piedTotalForMonth,
        ];
    }

    protected function getReferrersStatistics(): array
    {
        $userList = is_array($this->usersList()) ? collect($this->usersList()) : $this->usersList();
        return $userList
            ->map(function (User $user) {
                $employees = $this->withEmployees($user)->referrals;
                $user->deal_lead_conversion_ratio = $this->getRatio($user->deals, $user->leads);
                $user->applieds = count($employees);
                $user->appiled_deal_conversion_ratio = $this->getRatio($user->applieds, $user->deals);
                $user->absolute_earned = $this->getUserAbsoluteEarned($user);
                $user->month_earned = $this->getUserEarned($user);
                $user->referrer_earned = $this->getUserReferrersEarned($user, $this->date());
                $user->paid = $this->userPaid($user, $this->date());
                $user->trainees = $this->traineesWithStatistics($user);
                return $user;
            })
            ->toArray();
    }

    protected function date(): string
    {
        return Carbon::parse($this->filter['date'] ?? now()->format("Y-m-d"))->startOfMonth()->format("Y-m-d");
    }

    protected function getUserEarned(User $user, ?string $date = null): float
    {
        $trainees = $this->withTrainees($user, $date)->referrals;
        $total = $this->getSumFromTrainees($user->referrer_status, $trainees, DayType::DAY_TYPES['TRAINEE'], $this->date());
        $employees = $this->withEmployees($user, $date)->referrals;
        $total += count($employees) * 10000;
        return $total;
    }

    protected function withTrainees(User $user, ?string $date = null): User
    {
        return $user->load(['referrals' => fn(HasMany $query) => $query
            ->when($date, fn(Builder $query) => $query
                ->whereRelation('description', 'applied', '>=', $date))
            ->whereRelation('description', 'is_trainee', 1)]);
    }

    protected function withEmployees(User $user, ?string $date = null): User
    {
        return $user->load(['referrals' => fn(HasMany $query) => $query
            ->when($date, fn(Builder $query) => $query->whereRelation('description', 'applied', '>=', $date))
            ->whereRelation('description', 'is_trainee', 0)]);
    }

    protected function getRatio($convertible, $to): float
    {
        if ($to) {
            return ceil(($convertible / $to) * 100);
        }
        return 0;
    }

    protected function getUserAbsoluteEarned(User $user): float
    {
        $trainees = $this->withTrainees($user)->referrals;
        $total = $this->getSumFromTrainees($user->referrer_status, $trainees, DayType::DAY_TYPES['TRAINEE']);
        $employees = $this->withEmployees($user)->referrals;
        $total += count($employees) * 5000;

        /** @var User $employee */
        foreach ($employees as $employee) {
            $workedDaysCount = $employee->timetracking()
                ->selectRaw("*,DATE_FORMAT(enter, '%e') as date, TIMESTAMPDIFF(minute, `enter`, `exit`) as minutes")
//                ->where('minutes', '>=', 3 * 60)
                ->orderBy('id', 'ASC')
                ->count();
            $total += $this->calculateForDays($workedDaysCount);
            $subEmployees = $this->withEmployees($employee)->referrals;
            foreach ($subEmployees as $subEmployee) {
                $subWorkedDaysCount = $subEmployee->timetracking()
                    ->selectRaw("*,DATE_FORMAT(enter, '%e') as date, TIMESTAMPDIFF(minute, `enter`, `exit`) as minutes")
//                    ->where('minutes', '>=', 3 * 60)
                    ->orderBy('id', 'ASC')
                    ->count();
                $total += $this->calculateForDays($subWorkedDaysCount) > 0 ? 5000 : 0;
                $lastEmployees = $this->withEmployees($subEmployee)->referrals;
                foreach ($lastEmployees as $lastEmployee) {
                    $lastWorkedDaysCount = $lastEmployee->timetracking()
                        ->selectRaw("*,DATE_FORMAT(enter, '%e') as date, TIMESTAMPDIFF(minute, `enter`, `exit`) as minutes")
//                        ->where('minutes', '>=', 3 * 60)
                        ->orderBy('id', 'ASC')
                        ->count();
                    $total += $this->calculateForDays($lastWorkedDaysCount) > 0 ? 5000 : 0;
                }
            }
        }
        return $total;
    }

    protected function getSumFromTrainees(string $status, Collection $trainees, int $dateType, ?string $date = null)
    {
        $total = 0;
        $date = Carbon::parse($date);

        $percent = $this->getPercentByStatus($status);

        foreach ($trainees as $trainee) {
            $total += $trainee->daytypes()
                    ->when($date, fn(Builder $query) => $query
                        ->where('date', '>=', $date))
                    ->where('type', $dateType)
                    ->count() * (1000 + (1000 / 100) * $percent);
            $absences = $trainee->daytypes()
                    ->when($date, fn(Builder $query) => $query
                        ->where('date', '>=', $date))
                    ->where('type', $dateType)
                    ->count() * (1000 + (1000 / 100) * $percent);
            $total = $total - $absences;
        }

        return $total;
    }

    protected function getUserReferrersEarned(User $user, ?string $date = null): float
    {
        $total = 0;
        $percent = $this->getPercentByStatus($user->referrer_status);
        $employees = $this->withEmployees($user, $date)->referrals;
        foreach ($employees as $employee) {
            $workedDaysCount = $employee->timetracking()
                ->selectRaw("*,DATE_FORMAT(enter, '%e') as date, TIMESTAMPDIFF(minute, `enter`, `exit`) as minutes")
//                ->where('minutes', '>=', 3 * 60)
                ->orderBy('id', 'ASC')
                ->count();
            $total += $this->calculateForDays($workedDaysCount) > 0 ? 5000 + (5000 / 100) * $percent : 0;
            $subEmployees = $this->withEmployees($employee, $date)->referrals;
            foreach ($subEmployees as $subEmployee) {
                $subWorkedDaysCount = $subEmployee->timetracking()
                    ->selectRaw("*,DATE_FORMAT(enter, '%e') as date, TIMESTAMPDIFF(minute, `enter`, `exit`) as minutes")
//                    ->where('minutes', '>=', 3 * 60)
                    ->orderBy('id', 'ASC')
                    ->count();
                $total += $this->calculateForDays($subWorkedDaysCount) > 0 ? 2000 + (2000 / 100) * $percent : 0;
            }
        }
        return $total;
    }

    protected function userPaid(User $user, ?string $date = null)
    {
        return $user->salaries()
            ->when($date, fn(Builder $query) => $query
                ->whereDate('date', '>=', $date))
            ->sum('award');
    }

    protected function calculateForDays($workedDayCount): float|int
    {
        $count = $workedDayCount / 6; // get how many 6 days is worked the user
        if ($count < 1) {
            return 0;
        }
        // we divide 1 because for first six days referrer gets 10000 and for others six days 5000
        return 10000 + (($count - 1) * 5000);
    }

    protected function traineesWithStatistics(User $user, int $level = 1): array
    {
        $date = Carbon::parse($this->date());
        return $this->withEmployees($user)
            ->referrals
            ->map(function (User $trainee) use ($user, $date, $level) {
                $percent = $this->getPercentByStatus($trainee->referrer_status);
                $workedDaysCount = $trainee->timetracking()
                    ->selectRaw("*,DATE_FORMAT(enter, '%e') as date, TIMESTAMPDIFF(minute, `enter`, `exit`) as minutes")
//                    ->where('minutes', '>=', 3 * 60)
                    ->orderBy('id', 'ASC')
                    ->count();
                $count = $workedDaysCount / 6;
                $weeksWorked = [];
                for ($i = 1; $i <= $date->daysInMonth; $i++) {
                    $day = $trainee->daytypes->where('day', $i)->first();
                    $daytypes[$i] = $day?->type == 5 ? (1000 + ((1000 / 100) * $percent)) : 0;
                    if ($i < $count) {
                        $weeksWorked[$i] = 5000 + (5000 / 100) * $percent;
                    }
                }
                $trainee->daytypes = $daytypes;
                $trainee->weeksWorked = $weeksWorked;
                if ($level >= 3 || $this->withEmployees($user)
                        ->referrals->count() === 0
                ) {
                    return $trainee;
                }
                return $this->traineesWithStatistics($trainee, $level + 1);
            })
            ->toArray();
    }

    protected function getPercentByStatus(string $status): int
    {
        return match (true) {
            $status == ReferrerStatus::AMBASSADOR->serialize() => 10,
            $status == ReferrerStatus::ACTIVIST->serialize() => 10,
            default => 0,
        };
    }

    protected function usersList(): Collection|array
    {
        return User::query()
            ->WhereHas('referralLeads')
            ->withCount('referralLeads as leads')
            ->withCount(['referralLeads as deals' => fn(Builder $query) => $query
                ->where('deal_id', '>', 0)])
            ->withSum(['salaries as absolute_paid' => fn(Builder $query) => $query
                    ->where('resource', SalaryResourceType::REFERRAL)]
                , 'award')
            ->get();
    }
}
