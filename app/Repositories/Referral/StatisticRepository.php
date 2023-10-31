<?php

namespace App\Repositories\Referral;

use App\DayType;
use App\Enums\SalaryResourceType;
use App\Models\Bitrix\Lead;
use App\Salary;
use App\Service\Referral\Core\LeadTemplate;
use App\Timetracking;
use App\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

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
        $accepted = User::query()
            ->whereRelation('description', 'is_trainee', 0)
//            ->whereRelation('description', 'applied', '>=', $this->date())
            ->whereNotNull('referrer_id')
            ->count();

        $leads = Lead::query()
            ->where('segment', LeadTemplate::SEGMENT_ID)
            ->count();
        $deals = Lead::query()
            ->where('segment', LeadTemplate::SEGMENT_ID)
            ->where('deal_id', '>', 0)
            ->count();
        $piedTotalForMonth = Salary::query()
            ->where('date', '>=', $this->date())
            ->where('resource', SalaryResourceType::REFERRAL)
            ->where('is_paid', 1) // this means that salary was accepted!
            ->sum('award');

        $earnedTotalForMonth = Salary::query()
            ->where('date', '>=', $this->date())
            ->where('resource', SalaryResourceType::REFERRAL)
            ->sum('award');

        return [
            'employee_price' => $accepted ? $piedTotalForMonth / $accepted : 0,
            'deal_lead_conversion' => $leads ? ($deals / $leads) * 100 : 0,
            'applied_deal_conversion' => $deals ? ($accepted / $deals) * 100 : 0,
            'earned' => $earnedTotalForMonth,
            'paid' => $piedTotalForMonth,
        ];
    }

    protected function getReferrersStatistics(): array
    {
        $userList = is_array($this->usersList()) ? collect($this->usersList()) : $this->usersList();
        return $userList
            ->map(function (User $user) {
                $employees = $user->referrals()
                    ->whereRelation('description', 'is_trainee', 0)
//                    ->whereRelation('description', 'applied', '!=', 0)
                    ->get();
                $user->users = $this->schedule($user);
                $user->deal_lead_conversion_ratio = $this->getRatio($user->deals, $user->leads);
                $user->applieds = count($employees);
                $user->appiled_deal_conversion_ratio = $this->getRatio($user->applieds, $user->deals);
                $user->referrers_earned = $this->getUserReferrersEarned($user, $this->date());
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
        return $user->salaries()
            ->when($date, fn($query) => $query->where('date', '>=', $date))
            ->where('resource', SalaryResourceType::REFERRAL)
            ->sum('award');
    }


    protected function getRatio($convertible, $to): float
    {
        if ($to) {
            return ceil((100 * $convertible) / $to);
        }
        return 0;
    }

    protected function getUserReferrersEarned(User $user, ?string $date = null): float
    {
        $total = 0;
        $referrers = $user->referrals()
            ->whereHas('referralLeads')
            ->get();
        foreach ($referrers as $referrer) {
            $total += $this->getUserEarned($referrer, $date);
        }
        return $total;
    }

    protected function usersList(): Collection|array
    {
        return User::query()
            ->WhereHas('referralLeads')
            ->withCount('referralLeads as leads')
            ->with('referrals')
            ->withCount(['referralLeads as deals' => fn(Builder $query) => $query
                ->where('deal_id', '>', 0)])
            ->withSum(['salaries as absolute_paid' => fn(Builder $query) => $query
                    ->where('is_paid', 1)
                    ->where('resource', SalaryResourceType::REFERRAL)]
                , 'award')
            ->withSum(['salaries as absolute_earned' => fn(Builder $query) => $query
                    ->where('resource', SalaryResourceType::REFERRAL)]
                , 'award')
            ->withSum(['salaries as month_earned' => fn(Builder $query) => $query
                    ->where('date', '>=', $this->date())
                    ->where('resource', SalaryResourceType::REFERRAL)]
                , 'award')
            ->withSum(['salaries as month_paid' => fn(Builder $query) => $query
                    ->where('is_paid', 1)
                    ->where('date', '>=', $this->date())
                    ->where('resource', SalaryResourceType::REFERRAL)]
                , 'award')
            ->orderBy('leads', 'desc')
            ->get();
    }

    private function schedule(User $referrer)
    {
        $date = Carbon::parse($this->date());
        return $referrer->referrals()
            ->get()
            ->map(function (User $user) use ($date, $referrer) {
                $days = DayType::query()
                    ->selectRaw("*,DATE_FORMAT(date, '%e') as day")
                    ->where('user_id', $user->getKey())
                    ->whereMonth('date', '=', $date->month)
                    ->whereYear('date', $date->year)
                    ->get();

                $salaries = $this->getSalariesForReferral($referrer, $user);

                $forTrainee = $this->filterTraineeSalaries($salaries);

                $forWork = $this->filterEmployeesSalaries($salaries, $user);

                $appliedSalary = $this->filterCertificateSalary($salaries, $user);

                $schedule = array_merge(
                    $this->traineesDaily($date, $days, $forTrainee),
                    $this->employeeWeekly($user, $date, $forWork, $appliedSalary)
                );

                $user->datetypes = $schedule;
                return $user;
            });
    }

    private function getSalariesForReferral(User $referrer, User $referral): Collection
    {
        return $referrer->salaries()
            ->where('comment_award', $referral->getKey())
            ->where('resource', SalaryResourceType::REFERRAL)
            ->get();
    }

    private function filterTraineeSalaries(Collection $salaries): array
    {
        return $salaries->where('award', '<', 5000)->toArray();
    }

    private function filterEmployeesSalaries(Collection $salaries, User $user): array
    {
        return $salaries
            ->where('date', '!=', Carbon::parse($user->description()?->first()?->applied)->format("Y-m-d"))
            ->where('award', '>=', 5000)->toArray();
    }

    private function filterCertificateSalary($salaries, User $user)
    {
        $appliedAt = Carbon::parse($user->description()?->first()?->applied);
        return $salaries
            ->filter(fn(Salary $salary) => $salary->date->format("Y-m-d") === $appliedAt->format("Y-m-d"))
            ->first()?->toArray();
    }

    private function traineesDaily($date, $days, $forTrainee): array
    {
        $types = [];
        for ($i = 1; $i <= $date->daysInMonth; $i++) {
            $day = $days
                ->where('day', $i)
                ->first();
            if (is_null($day) || $day->type == DayType::DAY_TYPES['ABCENSE']) {
                $types[$i] = null;
            } elseif (in_array($day->type, [DayType::DAY_TYPES['TRAINEE'], DayType::DAY_TYPES['RETURNED']])) {
                $salary = [];
                foreach ($forTrainee as $item) {
                    if (Carbon::parse($item['date'])->format("Y-m-d") == $day->date->format("Y-m-d")) {
                        $salary = $item;
                    }
                }
                if (count($salary)) {
                    $types[$i] = [
                        'paid' => (bool)($salary['is_paid']) ?? false,
                        'sum' => $salary['award'] ?? 0,
                        'comment' => $salary['note'] ?? null,
                        'id' => $salary['id'] ?? null,
                    ];
                }
            }
        }
        return $types;
    }

    private function employeeWeekly(User $user, $date, $forWork, $appliedSalary): array
    {
        $toCheck = [1, 2, 3, 4, 6, 8, 12];
        $types['pass_certification'] = null;

        foreach ([1, 2, 3, 4, 6, 8, 12] as $week) {
            $types[$week . '_week'] = null;
        }

        if ($user->description()->first()?->is_trainee == 0) {
            if ($appliedSalary) {
                $types['pass_certification'] = [
                    'paid' => (bool)($appliedSalary['is_paid'] ?? null),
                    'sum' => $appliedSalary['award'] ?? null,
                    'comment' => $appliedSalary['note'] ?? null,
                    'id' => $appliedSalary['id'],
                ];
            }

            $timetracking = Timetracking::query()
                ->selectRaw("*,DATE_FORMAT(enter, '%e') as date, TIMESTAMPDIFF(minute, `enter`, `exit`) as minutes")
                ->where('user_id', $user->getKey())
                ->whereMonth('enter', '=', $date->month)
                ->whereYear('enter', $date->year)
                ->orderBy('id', 'ASC')
                ->get();

            foreach ($timetracking as $timer) {
                for ($i = 1; $i <= $timetracking->count() / 6; $i++) {
                    if ($i > 12) {
                        break;
                    }
                    if (in_array($i, $toCheck)) {
                        $current = [];
                        foreach ($forWork as $item) {
                            if (Carbon::parse($item['date'])->format("Y-m-d") == $timer->exit->format("Y-m-d")) {
                                $current = $item;
                            }
                        }
                        $types[$i . '_week'] = [
                            'paid' => (bool)($current['is_paid'] ?? null),
                            'sum' => $current['award'] ?? null,
                            'comment' => $current['note'] ?? null,
                            'date' => Carbon::parse($current['date'])->format("Y-m-d"),
                            'id' => $current['id'],
                        ];
                    }
                }
            }
        }
        return $types;
    }
}
