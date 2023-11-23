<?php

namespace App\Repositories\Referral;

use App\DayType;
use App\Service\Referral\Core\LeadTemplate;
use App\Service\Referral\Core\PaidType;
use App\Service\Referral\SalaryFilter;
use App\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

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
        $referrer = $this->referrer($user ?? auth()->user());
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
            ->withCount(['referrals as applied_count' => function ($query) {
                $query->whereRelation('description', 'is_trainee', 0);
            }])
            ->select(['id', 'name', 'last_name', 'referrer_status', 'img_url'])
            ->has('referrals', '>', 0)
            ->groupBy('users.id')
            ->take(5)
            ->get()
            ->toArray();
    }


    protected function referrer(User $user): User
    {
        /** @var User $referrer */
        $referrer = User::query()
            ->where('id', $user->getKey())
            ->with([
                'user_description' => function ($query) {
                    $query->select(['id', 'user_id', 'is_trainee']);
                }
            ])
            ->select([
                'id',
                'referrer_id',
                'name',
                'last_name',
                'referrer_status',
                'deleted_at',
                DB::raw('(SELECT SUM(amount) FROM referral_salaries WHERE users.id = referral_salaries.referrer_id AND is_paid = 1 AND date BETWEEN "' . $this->dateStart()->format("Y-m-d") . '" AND "' . $this->dateEnd()->format("Y-m-d") . '") AS month_paid'),
                DB::raw('(SELECT SUM(amount) FROM referral_salaries WHERE users.id = referral_salaries.referrer_id) AS absolute_earned'),
                DB::raw('(SELECT SUM(amount) FROM referral_salaries WHERE users.id = referral_salaries.referrer_id AND is_paid = 1) AS absolute_paid'),
                DB::raw('(SELECT SUM(amount) FROM referral_salaries WHERE users.id = referral_salaries.referrer_id AND date BETWEEN "' . $this->dateStart()->format("Y-m-d") . '" AND "' . $this->dateEnd()->format("Y-m-d") . '") AS month_earned'),
                DB::raw('(SELECT SUM(amount) FROM referral_salaries WHERE users.id = referral_salaries.referrer_id AND is_paid = 1 AND date BETWEEN "' . $this->dateStart()->format("Y-m-d") . '" AND "' . $this->dateEnd()->format("Y-m-d") . '") AS month_paid'),
                DB::raw('(SELECT SUM(amount) FROM referral_salaries WHERE users.id = referral_salaries.referrer_id AND is_paid = 1 AND date BETWEEN "' . $this->dateStart()->format("Y-m-d") . '" AND "' . $this->dateEnd()->format("Y-m-d") . '" AND type = "' . PaidType::FIRST_WORK->name . '") AS referrers_earned'),
                DB::raw('(SELECT SUM(amount) FROM referral_salaries WHERE users.id = referral_salaries.referrer_id AND date BETWEEN "' . $this->dateStart()->format("Y-m-d") . '" AND "' . $this->dateEnd()->format("Y-m-d") . '" AND amount IN (1000, 1100, 1500, 5000, 5500, 5750, 10000, 11000, 15000)) AS mine'),
                DB::raw('(SELECT COUNT(*) FROM users ref
                                INNER JOIN user_descriptions ON ref.id = user_descriptions.user_id 
                                WHERE ref.referrer_id = users.id AND user_descriptions.is_trainee = 0) AS applieds'),
                DB::raw('(SELECT COUNT(*) FROM bitrix_leads WHERE referrer_id = users.id AND segment = ' . LeadTemplate::SEGMENT_ID . ' AND deal_id > 0) AS leads'),
                DB::raw('(SELECT COUNT(*) FROM bitrix_leads WHERE referrer_id = users.id AND deal_id IS NOT NULL AND segment = ' . LeadTemplate::SEGMENT_ID . ') AS deals'),
            ])
            ->first();

        $referrer->deal_lead_conversion_ratio = $this->getRatio($referrer->referralLeads_count, $referrer->leads_count);
        $referrer->applied_deal_conversion_ratio = $this->getRatio($referrer->appliedReferrals_count, $referrer->referralLeads_count);

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
            ->withCount('referrals as referrals_count')
            ->with(['daytypes' => function (HasMany $query) {
                $query->selectRaw("id, user_id, type, date,DATE_FORMAT(date, '%e') as day")
                    ->whereMonth('date', '=', $this->dateStart()->month)
                    ->whereYear('date', $this->dateStart()->year);
            }])
            ->with(['timetracking' => function (HasMany $query) {
                $query->select(["enter", "exit", "id", "user_id"])
                    ->whereMonth('enter', '=', $this->dateStart()->month)
                    ->whereYear('enter', $this->dateStart()->year);
            }])
            ->with(['referrerSalaries' => function (HasMany $query) use ($referrer) {
                $query->where("referrer_id", $referrer->getKey());
                $query->select(["referrer_id", 'date', 'amount', 'comment', 'referral_id', 'type', 'id']);
            }])
            ->with(['user_description' => fn($query) => $query->select(['id', 'user_id', 'is_trainee'])])
            ->orderBy("created_at")
            ->get()
            ->map(function (User $referral) use ($referrer, $step) {

                $days = $referral->daytypes;

                $salaries = $referral->referrerSalaries;
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

                if ($referral->referrals_count) {

                    if ($step <= 3) {
                        $referral->referrals = $this->referrals($referral, $step + 1);
                    }
                }

                return $referral;
            });
    }

    private function getReferralSalaries(User $referrer, User $referral): Collection
    {
        return $referrer->referralSalaries
            ->where([
                'referral_id' => $referral->getKey(),
            ]);
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
        $timeTracking = $this->getReferralTimeTracking($referral);

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

    private function getReferralTimeTracking(User $referral): Collection
    {
        return $referral->timetracking
            ->where('user_id', $referral->getKey());
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