<?php

namespace App\Repositories\Referral;

use App\DayType;
use App\Models\Referral\ReferralSalary;
use App\Service\Referral\Core\LeadTemplate;
use App\Service\Referral\Core\PaidType;
use App\Service\Referral\SalaryFilter;
use App\Timetracking;
use App\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

class StatisticRepository implements StatisticRepositoryInterface
{
    protected array $filter = [];

    public function __construct(
        private readonly SalaryFilter $salaryFilter
    )
    {
    }

    public function statistic(array $filter): array
    {
        $this->filter = $filter;
        $described = $this->described();
        return [
            'pivot' => $this->pivot($described),
            'referrers' => $described
        ];
    }

    protected function pivot(array $described): array
    {
        $deal_lead_conversion = 0;
        $applied_deal_conversion = 0;
        $countForDeals = 0;
        $countForApplied = 0;

        foreach ($described as $referer) {
            $deal_lead_conversion += $referer['deal_lead_conversion_ratio'];
            $countForDeals += ($referer['leads'] > 0) ? 1 : 0;
            $applied_deal_conversion += $referer['appiled_deal_conversion_ratio'];
            $countForApplied += ($referer['deals'] > 0) ? 1 : 0;
        }

        $deal_lead_conversion = $countForDeals ? $deal_lead_conversion / $countForDeals : 0;
        $applied_deal_conversion = $countForApplied ? $applied_deal_conversion / $countForApplied : 0;

        $accepted = User::withTrashed()
            ->select('id', 'referrer_id')
            ->whereRelation('description', 'is_trainee', 0)
            ->whereNotNull('referrer_id')
            ->count();

        $dateStart = $this->dateStart()->format("Y-m-d");
        $dateEnd = $this->dateEnd()->format("Y-m-d");

        $salaries = ReferralSalary::query()->get();

        $paidTotal = $salaries
            ->where('is_paid', 1)
            ->sum('amount');

        $earnedTotalForMonth = $salaries
            ->whereBetween('date', [$dateStart, $dateEnd])
            ->sum('amount');

        $paidTotalForMonth = $salaries
            ->whereBetween('date', [$dateStart, $dateEnd])
            ->where('is_paid', 1)
            ->sum('amount');

        return [
            'employee_price' => $accepted ? $paidTotal / $accepted : 0,
            'deal_lead_conversion' => $deal_lead_conversion,
            'applied_deal_conversion' => $applied_deal_conversion,
            'earned' => $earnedTotalForMonth,
            'paid' => $paidTotalForMonth,
        ];
    }

    protected function described(bool $schedule = false): array
    {
        return $this->baseQuery()
            ->get()
            ->each(function (User $user) use ($schedule) {
                $user->deal_lead_conversion_ratio = $this->getRatio($user->deals, $user->leads);
                $user->appiled_deal_conversion_ratio = $this->getRatio($user->applieds, $user->deals);
                $user->referrers_earned = $this->getReferralsEarned($user);
                if ($schedule) {
                    $user->referrals = $this->schedule($user);
                }
            })
            ->toArray();
    }

    protected function baseQuery(): Builder
    {
        $bindings = [
            $this->dateStart()->format("Y-m-d"),
            $this->dateEnd()->format("Y-m-d"),
        ];

        return User::query()
            ->select(['id', 'referrer_id', 'name', 'last_name', 'referrer_status', 'deleted_at'])
            ->WhereHas('referralLeads')
            ->withCount(['appliedReferrals as applieds' => fn($query) => $query
                ->whereRelation('description', 'is_trainee', 0)])
            ->withCount(['referralLeads as deals' => fn($query) => $query
                ->where('segment', LeadTemplate::SEGMENT_ID)
                ->where('deal_id', '>', 0)])
            ->withCount(['referralLeads as leads' => fn($query) => $query
                ->where('segment', LeadTemplate::SEGMENT_ID)])
            ->with(['user_description' => fn($query) => $query->select(['id', 'user_id', 'is_trainee'])])
            ->selectRaw('(
            SELECT SUM(amount)
            FROM referral_salaries
            WHERE users.id = referral_salaries.referrer_id
                AND is_paid = true
                AND date BETWEEN ? AND ?
        ) AS month_paid', $bindings)
            ->selectRaw('(
            SELECT SUM(amount)
            FROM referral_salaries
            WHERE users.id = referral_salaries.referrer_id
        ) AS absolute_earned')
            ->selectRaw('(
            SELECT SUM(amount)
            FROM referral_salaries
            WHERE users.id = referral_salaries.referrer_id
                AND date BETWEEN ? AND ?
        ) AS month_earned', $bindings)
            ->orderBy('leads', 'desc');
    }


    private function schedule(User $referrer, int $step = 1): array
    {
        return $referrer->referrals()
            ->select(['id', 'referrer_id', 'referrer_status'])
            ->with(['daytypes' => function (HasMany $query) {
                $query->selectRaw("*,DATE_FORMAT(date, '%e') as day")
                    ->whereMonth('date', '=', $this->dateStart()->month)
                    ->whereYear('date', $this->dateStart()->year);
            }])
            ->with('referralSalaries')
            ->with(['user_description' => fn($query) => $query->select(['id', 'user_id', 'is_trainee'])])
            ->withCount('referrals')
            ->orderBy("created_at")
            ->get()
            ->each(function (User $referral) use ($referrer, $step) {

                $attestation = [];
                $training = [];

                $days = $referrer->daytypes;

                $salaries = $referrer->referralSalaries->where('referral_id', $referral->getKey());

                $this->salaryFilter->forThisCollection($salaries);
                if ($step = 1) {
                    $attestation = $this->salaryFilter->filter(PaidType::ATTESTATION);
                    $training = $this->salaryFilter->filter(PaidType::TRAINEE);
                }
                $working = $this->salaryFilter->filter(PaidType::WORK);

                $referral->is_trainee = $referral->user_description?->is_trainee;

                $referral->daytypes = array_merge(
                    $this->traineesDaily($days, $training),
                    $this->attestation($attestation),
                    $this->employeeWeekly($referral, $working)
                );

                if ($referral->referrals_count) {
                    if ($step <= 3) {
                        $referral->referrals = $this->schedule($referral, $step + 1);
                    }
                }
            })
            ->toArray();
    }

    protected function dateStart(): Carbon
    {
        $this->filter['date'] = $this->filter['date'] ?? now()->format("Y-m-d");
        return Carbon::parse($this->filter['date'])
            ->startOfMonth()
            ->copy();
    }

    protected function dateEnd(): Carbon
    {
        $this->filter['date'] = $this->filter['date'] ?? now()->format("Y-m-d");
        return Carbon::parse($this->filter['date'])
            ->endOfMonth()
            ->copy();
    }

    protected function getUserEarned(User $user, ?Carbon $dateStart = null, ?Carbon $dateEnd = null): float
    {
        return $user->referralSalaries
            ->when($dateStart, fn($query) => $query->where('date', '>=', $dateStart->format("Y-m-d")))
            ->when($dateEnd, fn($query) => $query->where('date', '<=', $dateEnd->format("Y-m-d")))
            ->sum('amount');
    }

    protected function getRatio($convertible, $to): float
    {
        if ($to) {
            return ceil((100 * $convertible) / $to);
        }
        return 0;
    }

    protected function getReferralsEarned(User $user): float
    {
        $total = 0;
        /** @var Collection<User> $referrers */
        $referrers = $user->referrals()
            ->whereHas('referralLeads')
            ->get();
        foreach ($referrers as $referrer) {
            $total += $referrer->referralSalaries()
                ->where(function ($query) {
                    $query->where('type', PaidType::FIRST_WORK->name);
                    $query->whereDate('date', $this->dateStart()->format("Y-m-d"));
                    $query->whereDate('date', '<=', $this->dateEnd()->format("Y-m-d"));
                })
                ->sum("amount");
        }
        return $total;
    }

    private function traineesDaily($days, $training): array
    {
        $types = [];
        for ($i = 1; $i <= $this->dateStart()->daysInMonth; $i++) {
            $day = $this->getDay($days, $i);
            if ($this->isAbsence($day)) {
                $types[$i] = null;
            } elseif ($this->isTrainee($day)) {
                $types[$i] = $this->countTrainingDays($training, $day);
            }
        }
        return $types;
    }

    private function attestation($attestation): array
    {
        $appliedSalary = current($attestation->toArray());

        if (!$appliedSalary) {
            return [];
        }

        return [
            'pass_certification' => $this->parseSalary($appliedSalary)
        ];
    }

    private function employeeWeekly(User $referral, Collection $working): array
    {
        $weeksToTrack = [1, 2, 3, 4, 6, 8, 12];
        $weekTemplate = $this->createWeekTemplate($weeksToTrack);
        $timeTracking = $this->getReferralTimeTracking($referral, $this->dateStart());

        foreach ($timeTracking as $tracker) {
            for ($week = 1; $week <= $weeksToTrack; $week++) {
                // If the week is beyond 12, exit the loop
                if ($week > 12) {
                    break;
                }

                // Check if the current week is in the weeks to track
                if (in_array($week, $weeksToTrack)) {
                    $current = [];

                    // Find the working item with the same date as the exit date
                    foreach ($working as $item) {
                        if ($this->isSameDate(Carbon::parse($item['date']), $tracker->exit)) {
                            $current = $item->toArray();
                            break; // Exit the loop once found
                        }
                    }

                    // Store the parsed salary in the result array
                    $weekTemplate[$week . '_week'] = $this->parseSalary($current);
                }
            }
        }

        return $weekTemplate;
    }

    private function getReferralTimeTracking(User $referral, Carbon $date): Collection
    {
        return Timetracking::query()
            ->selectRaw("*,DATE_FORMAT(enter, '%e') as date, TIMESTAMPDIFF(minute, `enter`, `exit`) as minutes")
            ->where('user_id', $referral->getKey())
            ->whereMonth('enter', '=', $date->month)
            ->whereYear('enter', $date->year)
            ->orderBy('id', 'ASC')
            ->get();
    }

    private function isAbsence(?DayType $day): bool
    {
        return is_null($day) || $day->type == DayType::DAY_TYPES['ABCENSE'];
    }

    private function isTrainee(?DayType $day): bool
    {
        if (!$day) {
            return false;
        }
        return in_array($day->type, [DayType::DAY_TYPES['TRAINEE'], DayType::DAY_TYPES['RETURNED']]);
    }

    private function getDay($days, int $day): ?DayType
    {
        /** @var DayType $day */
        return $days
            ->where('day', $day)
            ->first();
    }

    private function countTrainingDays($training, DayType $day): ?array
    {
        $salary = [];
        foreach ($training as $item) {
            if ($this->isSameDate(Carbon::parse($item['date']), $day->date)) {
                $salary = $item->toArray();
            }
        }

        if (!count($salary)) {
            return null;
        }

        return $this->parseSalary($salary);
    }

    private function isSameDate(Carbon $first, Carbon $second): bool
    {
        return Carbon::parse($first)->format("Y-m-d") == $second->format("Y-m-d");
    }

    private function createWeekTemplate(array $weeks): array
    {
        $types = [];

        foreach ($weeks as $week) {
            $types[$week . '_week'] = null;
        }
        return $types;
    }

    private function parseSalary(?array $current): array
    {
        return [
            'paid' => (bool)($current['is_paid'] ?? null),
            'sum' => $current['amount'] ?? null,
            'comment' => $current['comment'] ?? null,
            'id' => $current['id'] ?? null,
            'date' => $current['date'] ?? null,
        ];
    }
}
