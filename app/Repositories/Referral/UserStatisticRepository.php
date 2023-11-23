<?php

namespace App\Repositories\Referral;

use App\DayType;
use App\Facade\Referring;
use App\Service\Referral\Core\LeadTemplate;
use App\Service\Referral\Core\PaidType;
use App\Service\Referral\SalaryFilter;
use App\Timetracking;
use App\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

class UserStatisticRepository implements UserStatisticRepositoryInterface
{
    protected array $filter = [];

    public function __construct(
        private readonly SalaryFilter $salaryFilter
    )
    {
    }

    public function statistic(array $filter, ?User $user = null): array
    {
        $this->filter = $filter;
        $referrer = $this->referrer(auth()->user());
        $referrerToArray = $referrer->toArray();

        return [
            'tops' => $this->tops(),
            'referrer' => $referrer,
            'referrals' => $this->referrals($referrer),
            'mine' => $referrerToArray['mine'],
            'from_referrals' => $referrerToArray['referrers_earned'],
            'absolute' => $referrerToArray['absolute_earned'],
        ];
    }

    private function tops(): array
    {
        return User::query()
            ->select(['id', 'name', 'last_name', 'referrer_status', 'img_url'])
            ->withCount(['referrals as applied_count' => function ($query) {
                $query->whereRelation('description', 'is_trainee', 0);
            }])
            ->groupBy('users.id')
            ->having('applied_count', '>', 0)
            ->take(5)
            ->orderBy('applied_count', 'desc')
            ->get()
            ->toArray();
    }

    protected function referrer(User $user): User
    {
        $bindings = [
            $this->dateStart()->format("Y-m-d"),
            $this->dateEnd()->format("Y-m-d"),
        ];

        /** @var User $referrer */
        $referrer = User::query()
            ->where('id', $user->getKey())
            ->select(['id', 'referrer_id', 'name', 'last_name', 'referrer_status', 'deleted_at'])
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
                AND is_paid = 1
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
            AND is_paid = 1
        ) AS absolute_paid')
            ->selectRaw('(
            SELECT SUM(amount)
            FROM referral_salaries
            WHERE users.id = referral_salaries.referrer_id
                AND date BETWEEN ? AND ?
        ) AS month_earned', $bindings)
            ->selectRaw('(
            SELECT SUM(amount)
            FROM referral_salaries
            WHERE users.id = referral_salaries.referrer_id
                AND is_paid = 1
                AND date BETWEEN ? AND ?
        ) AS month_paid', $bindings)
            ->selectRaw('(
            SELECT SUM(amount)
            FROM referral_salaries
            WHERE users.id = referral_salaries.referrer_id
                AND is_paid = 1
                AND date BETWEEN ? AND ?
                AND type = ?
        ) AS referrers_earned', [
                ...$bindings,
                PaidType::FIRST_WORK->name
            ])
            ->selectRaw('(
    SELECT SUM(amount)
    FROM referral_salaries
    WHERE users.id = referral_salaries.referrer_id
        AND date BETWEEN ? AND ?
        AND amount IN (1000, 1100, 1500, 5000, 5500, 5750, 10000, 11000, 15000)
) AS mine',
                $bindings
            )
            ->orderBy('leads', 'desc')
            ->first();

        $referrer->deal_lead_conversion_ratio = $this->getRatio($referrer->deals, $referrer->leads);
        $referrer->appiled_deal_conversion_ratio = $this->getRatio($referrer->applieds, $referrer->deals);
        return $referrer;
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


    protected function getRatio($convertible, $to): float
    {
        if ($to) {
            return ceil((100 * $convertible) / $to);
        }
        return 0;
    }

    private function referrals(User $referrer, int $step = 1)
    {
        return $referrer->referrals()
            ->select(['name', 'last_name', 'referrer_id', 'id'])
            ->with(['user_description' => fn($query) => $query->select(['id', 'user_id', 'is_trainee'])])
            ->orderBy("created_at")
            ->get()
            ->map(function (User $referral) use ($referrer, $step) {

                Referring::touchReferrerStatus($referral); // before get

                $days = $this->getReferralDayTypes($referral);

                $salaries = $this->getReferralSalaries($referrer, $referral);

                $this->salaryFilter->forThisCollection($salaries);

                $training = $this->salaryFilter->filter(PaidType::TRAINEE);
                $working = $this->salaryFilter->filter(PaidType::WORK);
                $attestation = $this->salaryFilter->filter(PaidType::ATTESTATION);

                $referral->is_trainee = $referral->user_description?->is_trainee;
                $referral->datetypes = array_merge(
                    $this->traineesDaily($days, $training),
                    $this->attestation($attestation),
                    $this->employeeWeekly($referral, $working)
                );

                if ($referral->referrals()->count()) {

                    if ($step <= 3) {
                        $referral->referrals = $this->referrals($referral, $step + 1);
                    }
                }

                return $referral;
            });
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
            'date' => $current['date'] ?? null,
        ];
    }
}