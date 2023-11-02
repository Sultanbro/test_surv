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
            if ($referer['leads'] > 0) {
                ++$countForDeals;
            }
            $applied_deal_conversion += $referer['appiled_deal_conversion_ratio'];
            if ($referer['deals'] > 0) {
                ++$countForApplied;
            }
        }

        $deal_lead_conversion = $deal_lead_conversion / $countForDeals;
        $applied_deal_conversion = $applied_deal_conversion / $countForApplied;

        $accepted = User::query()
            ->whereRelation('description', 'is_trainee', 0)
            ->whereNotNull('referrer_id')
            ->count();

        $paidTotal = ReferralSalary::query()
            ->where('is_paid', 1) // this means that salary was accepted!
            ->sum('amount');

        $paidTotalForMonth = ReferralSalary::query()
            ->whereDate('date', '>=', $this->dateStart()->format("Y-m-d"))
            ->whereDate('date', '<=', $this->dateEnd()->format("Y-m-d"))
            ->where('is_paid', 1) // this means that salary was accepted!
            ->sum('amount');

        $earnedTotalForMonth = ReferralSalary::query()
            ->whereDate('date', '>=', $this->dateStart()->format("Y-m-d"))
            ->whereDate('date', '<=', $this->dateEnd()->format("Y-m-d"))
            ->sum('amount');

        return [
            'employee_price' => $accepted ? $paidTotal / $accepted : 0,
            'deal_lead_conversion' => $deal_lead_conversion,
            'applied_deal_conversion' => $applied_deal_conversion,
            'earned' => $earnedTotalForMonth,
            'paid' => $paidTotalForMonth,
        ];
    }

    protected function described(): array
    {
        return $this->baseQuery()
            ->get()
            ->map(function (User $user) {
                $applies = $this->getAppliedReferrals($user);
                $user->month_paid = $user->referralSalaries()
                    ->where('is_paid', true)
                    ->whereDate('date', '>=', $this->dateStart()->format("Y-m-d"))
                    ->whereDate('date', '<=', $this->dateEnd()->format("Y-m-d"))
                    ->sum("amount");
                $user->absolute_earned = $user->referralSalaries
                    ->sum("amount");
                $user->month_earned = $user->referralSalaries()
                    ->whereDate('date', '>=', $this->dateStart()->format("Y-m-d"))
                    ->whereDate('date', '<=', $this->dateEnd()->format("Y-m-d"))
                    ->sum("amount");
                $user->deal_lead_conversion_ratio = $this->getRatio($user->deals, $user->leads);
                $user->appiled_deal_conversion_ratio = $this->getRatio($applies->count(), $user->deals);
                $user->applieds = $applies->count();
                $user->referrers_earned = $this->getReferralsEarned($user);
                $user->users = $this->schedule($user);
                return $user;
            })
            ->toArray();
    }

    protected function baseQuery(): Builder
    {
        return User::query()
            ->WhereHas('referralLeads')
            ->with(['referrals', 'referralSalaries'])
            ->withCount(['referralLeads as deals' => fn($query) => $query
                ->where('segment', LeadTemplate::SEGMENT_ID)
                ->where('deal_id', '>', 0)])
            ->withCount(['referralLeads as leads' => fn($query) => $query
                ->where('segment', LeadTemplate::SEGMENT_ID)])
            ->orderBy('leads', 'desc');
    }

    private function schedule(User $referrer)
    {
        return $referrer->referrals()
            ->get()
            ->map(function (User $referral) use ($referrer) {

                $days = $this->getReferralDayTypes($referral);

                $salaries = $this->getReferralSalaries($referrer, $referral);

                $this->salaryFilter->forThisCollection($salaries);

                $training = $this->salaryFilter->filter(PaidType::TRAINEE);
                $working = $this->salaryFilter->filter(PaidType::WORK);
                $attestation = $this->salaryFilter->filter(PaidType::ATTESTATION);
                $referral->datetypes = array_merge(
                    $this->traineesDaily($days, $training),
                    $this->attestation($attestation),
                    $this->employeeWeekly($referral, $working)
                );

                if ($referral->referrals()->count()) {
                    $referral->users = $this->schedule($referral);
                }

                return $referral;
            });
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
        return $user->referralSalaries()
            ->when($dateStart, fn($query) => $query->whereDate('date', '>=', $dateStart->format("Y-m-d")))
            ->when($dateEnd, fn($query) => $query->whereDate('date', '<=', $dateEnd->format("Y-m-d")))
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

    private function getReferralSalaries(User $referrer, User $referral): Collection
    {
        return $referrer->referralSalaries()
            ->where([
                'referral_id' => $referral->getKey(),
                'referrer_id' => $referrer->getKey()
            ])->get();
    }

    private function traineesDaily(Collection $days, $training): array
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

    private function getAppliedReferrals(User $user): Collection
    {
        return $user->referrals()
            ->whereRelation('description', 'is_trainee', 0)
            ->get();
    }

    private function getReferralDayTypes(User $referral): Collection
    {
        return DayType::query()
            ->selectRaw("*,DATE_FORMAT(date, '%e') as day")
            ->where('user_id', $referral->getKey())
            ->whereMonth('date', '=', $this->dateStart()->month)
            ->whereYear('date', $this->dateStart()->year)
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

    private function getDay(Collection $days, int $day): ?DayType
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
        ];
    }
}
