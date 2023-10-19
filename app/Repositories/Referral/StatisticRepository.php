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
            ->whereRelation('description', 'applied', '>=', $this->date())
            ->whereNotNull('referrer_id')
            ->count();

        $leads = Lead::query()
            ->where('segment', LeadTemplate::SEGMENT_ID)
            ->count();

        $deals = Lead::query()
            ->where('segment', LeadTemplate::SEGMENT_ID)
            ->whereNot('deal_id', 0)
            ->count();
        $piedTotalForMonth = Salary::query()
            ->where('date', '>=', $this->date())
            ->where('resource', SalaryResourceType::REFERRAL)
            ->where('is_paid', 1)
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
                    ->whereRelation('description', 'applied', '!=', 0)
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
            return ceil(($convertible / $to) * 100);
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
                $dates = DayType::query()
                    ->selectRaw("*,DATE_FORMAT(date, '%e') as day")
                    ->where('user_id', $user->getKey())
                    ->whereMonth('date', '=', $date->month)
                    ->whereYear('date', $date->year)
                    ->get();
                $salaries = $referrer->salaries()
                    ->where('comment_award', $user->getKey())
                    ->where('resource', SalaryResourceType::REFERRAL)
                    ->get();

                $forTrainee = $salaries->where('award', '<', 5000)->toArray();

                $forWork = $salaries
                    ->where('date', '!=', Carbon::parse($user->description()?->first()?->applied)->format("Y-m-d"))
                    ->where('award', '>=', 5000)->toArray();

                $forStat = $salaries
                    ->filter(fn(Salary $salary) => $salary->date->format("Y-m-d") === Carbon::parse($user->description()?->first()?->applied)->format("Y-m-d"))
                    ->first()?->toArray();
                for ($i = 1; $i <= $date->daysInMonth; $i++) {
                    $day = $dates
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
                        $types[$i] = [
                            'paid' => (bool)($salary['is_paid']),
                            'sum' => $salary['award'],
                            'comment' => $salary['note'],
                            'date' => Carbon::parse($salary['date'])->format("Y-m-d"),
                        ];
                    }
                }
                $toCheck = [1, 2, 3, 4, 6, 8, 12];
                $types['pass_certification'] = null;

                foreach ([1, 2, 3, 4, 6, 8, 12] as $week) {
                    $types[$week . '_week'] = null;
                }

                if ($user->description()->first()?->is_trainee == 0) {
                    if ($forStat) {
                        $types['pass_certification'] = [
                            'paid' => (bool)($forStat['is_paid'] ?? null),
                            'sum' => $forStat['award'] ?? null,
                            'comment' => $forStat['note'] ?? null,
                            'date' => Carbon::parse($forStat['date'])->format("Y-m-d"),
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
                                ];
                            }
                        }
                    }
                }
                $user->datetypes = $types;
                return $user;
            });
    }
}
