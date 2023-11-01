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
        return [
            'pivot' => $this->pivot(),
            'referrers' => $this->forEach()
        ];
    }

    protected function pivot(): array
    {
        $leads = [];
        $deal_lead_conversion = 0;
        $applied_deal_conversion = 0;
        $count = 1;

        foreach ($this->forEach() as $key => $referer) {
            $deal_lead_conversion += $referer['deal_lead_conversion_ratio'];
            if ($referer['deal_lead_conversion_ratio'] > 0) {
                $count += $key;
            }
        }

        $deal_lead_conversion = $deal_lead_conversion / 100 * $count;
        $count = 1;

        foreach ($this->forEach() as $key => $referer) {
            $applied_deal_conversion += $referer['appiled_deal_conversion_ratio'];
            if ($referer['appiled_deal_conversion_ratio'] > 0) {
                $count += $key;
            }
        }

        $applied_deal_conversion = $applied_deal_conversion / 100 * $count;

        $accepted = User::query()
            ->whereRelation('description', 'is_trainee', 0)
            ->whereNotNull('referrer_id')
            ->count();

        $piedTotalForMonth = ReferralSalary::query()
            ->where('date', '>=', $this->date())
            ->where('is_paid', 1) // this means that salary was accepted!
            ->sum('amount');

        $earnedTotalForMonth = ReferralSalary::query()
            ->where('date', '>=', $this->date())
            ->sum('amount');

        return [
            'employee_price' => $accepted ? $piedTotalForMonth / $accepted : 0,
            'deal_lead_conversion' => $deal_lead_conversion,
            'applied_deal_conversion' => $applied_deal_conversion,
            'earned' => $earnedTotalForMonth,
            'paid' => $piedTotalForMonth,
        ];
    }

    protected function forEach(): array
    {
        $userList = is_array($this->usersList()) ? collect($this->usersList()) : $this->usersList();
        return $userList
            ->map(function (User $user) {
                $applieds = $this->getAppliedReferrals($user);
                $user->deal_lead_conversion_ratio = $this->getRatio($user->deals, $user->leads);
                $user->appiled_deal_conversion_ratio = $this->getRatio($applieds->count(), $user->deals);
                $user->applieds = $applieds->count();
                $user->referrers_earned = $this->getUserReferrersEarned($user, $this->date());
                $user->users = $this->schedule($user);
                return $user;
            })
            ->toArray();
    }

    protected function usersList(): Collection|array
    {
        return User::query()
            ->WhereHas('referralLeads')
            ->with('referrals')
            ->withCount(['referralLeads as deals' => fn($query) => $query
                ->where('segment', LeadTemplate::SEGMENT_ID)
                ->where('deal_id', '>', 0)])
            ->withCount(['referralLeads as leads' => fn($query) => $query
                ->where('segment', LeadTemplate::SEGMENT_ID)])
            ->withSum(['referralSalaries as absolute_paid' => fn($query) => $query
                    ->where('is_paid', 1)]
                , 'amount')
            ->withSum(['referralSalaries as absolute_earned' => fn($query) => $query]
                , 'amount')
            ->withSum(['referralSalaries as month_earned' => fn($query) => $query
                    ->where('date', '>=', $this->date())]
                , 'amount')
            ->withSum(['referralSalaries as month_paid' => fn($query) => $query
                    ->where('is_paid', 1)
                    ->where('date', '>=', $this->date())]
                , 'amount')
            ->orderBy('leads', 'desc')
            ->get();
    }

    private function schedule(User $referrer)
    {
        $date = Carbon::parse($this->date());
        return $referrer->referrals()
            ->get()
            ->map(function (User $referral) use ($date, $referrer) {

                $days = $this->getReferralDayTypes($referral, $date);

                $salaries = $this->getReferralSalaries($referrer, $referral);

                $this->salaryFilter->forThisCollection($salaries);

                $training = $this->salaryFilter->filter(PaidType::TRAINEE);
                $working = $this->salaryFilter->filter(PaidType::WORK);
                $attestation = $this->salaryFilter->filter(PaidType::ATTESTATION);
                $referral->datetypes = array_merge(
                    $this->traineesDaily($date, $days, $training),
                    $this->attestation($attestation),
                    $this->employeeWeekly($referral, $date, $working)
                );

                return $referral;
            });
    }

    protected function date(): string
    {
        return Carbon::parse($this->filter['date'] ?? now()->format("Y-m-d"))->startOfMonth()->format("Y-m-d");
    }

    protected function getUserEarned(User $user, ?string $date = null): float
    {
        return $user->referralSalaries()
            ->when($date, fn($query) => $query->where('date', '>=', $date))
            ->sum('amount');
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

    private function getReferralSalaries(User $referrer, User $referral): Collection
    {
        return $referrer->referralSalaries()
            ->where('referral_id', $referral->getKey())
            ->where('referrer_id', $referrer->getKey())
            ->get();
    }

    private function traineesDaily(Carbon $date, Collection $days, $training): array
    {
        $types = [];
        for ($i = 1; $i <= $date->daysInMonth; $i++) {
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

    private function employeeWeekly(User $referral, Carbon $date, Collection $working): array
    {
        $weeksToTrack = [1, 2, 3, 4, 6, 8, 12];
        $weekTemplate = $this->createWeeksTemplate($weeksToTrack);
        $timeTracking = $this->getReferralTimeTracking($referral, $date);

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

    private function getReferralDayTypes(User $referral, Carbon $date): Collection
    {
        return DayType::query()
            ->selectRaw("*,DATE_FORMAT(date, '%e') as day")
            ->where('user_id', $referral->getKey())
            ->whereMonth('date', '=', $date->month)
            ->whereYear('date', $date->year)
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

    private function createWeeksTemplate(array $weeks): array
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
