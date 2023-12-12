<?php

namespace App\Repositories\Referral;

use App\DayType;
use App\Service\Referral\Core\LeadTemplate;
use App\Service\Referral\Core\PaidType;
use App\Service\Referral\SalaryFilter;
use App\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
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
        $users = User::query()
            ->whereHas('referrals')
            ->withCount(['referrals as applieds' => function ($query) {
                $query->whereRelation('description', 'is_trainee', 0);
            }])
            ->select(['id', 'name', 'last_name', 'referrer_status', 'img_url'])
            ->groupBy('users.id')
            ->take(5)
            ->get();

        return $users->map(function ($user) {
            return [
                'id' => $user->id,
                'name' => $user->name,
                'last_name' => $user->last_name,
                'referrer_status' => $user->referrer_status,
                'img_url' => $user->img_url,
                'applieds' => $user->applieds, // Accessing the count as an attribute
            ];
        })->toArray();
    }


    protected function referrer(User $user): User
    {
        /** @var User $referrer */
        $startDate = $this->dateStart()->format("Y-m-d");
        $endDate = $this->dateEnd()->format("Y-m-d");
        $segmentId = LeadTemplate::SEGMENT_ID;
        $paidTypeFirstWork = PaidType::FIRST_WORK->name;

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
                DB::raw("(SELECT SUM(amount) FROM referral_salaries WHERE users.id = referral_salaries.referrer_id) AS absolute_earned"),
                DB::raw("(SELECT SUM(amount) FROM referral_salaries WHERE users.id = referral_salaries.referrer_id AND is_paid = 1) AS absolute_paid"),
                DB::raw("(SELECT SUM(amount) FROM referral_salaries WHERE users.id = referral_salaries.referrer_id AND STR_TO_DATE(date, '%Y-%m-%d') BETWEEN '{$startDate}' AND '{$endDate}') AS month_earned"),
                DB::raw("(SELECT SUM(amount) FROM referral_salaries WHERE users.id = referral_salaries.referrer_id AND is_paid = 1 AND STR_TO_DATE(date, '%Y-%m-%d') BETWEEN '{$startDate}' AND '{$endDate}') AS month_paid"),
                DB::raw("(SELECT SUM(amount) FROM referral_salaries WHERE users.id = referral_salaries.referrer_id AND is_paid = 1 AND STR_TO_DATE(date, '%Y-%m-%d') BETWEEN '{$startDate}' AND '{$endDate}' AND type = '{$paidTypeFirstWork}' AND  amount < 10000) AS referrers_earned"),
                DB::raw("(SELECT SUM(amount) FROM referral_salaries WHERE users.id = referral_salaries.referrer_id AND STR_TO_DATE(date, '%Y-%m-%d') BETWEEN '{$startDate}' AND '{$endDate}' AND amount IN (1000, 1100, 1500, 5000, 5500, 5750, 10000, 11000, 15000)) AS mine"),
                DB::raw("(SELECT COUNT(*) FROM users ref
                    INNER JOIN user_descriptions ON ref.id = user_descriptions.user_id 
                    WHERE ref.referrer_id = users.id AND user_descriptions.is_trainee = 0 AND ref.deleted_at IS NULL ) AS applieds"),
                DB::raw("(SELECT COUNT(*) FROM bitrix_leads WHERE referrer_id = users.id AND segment = {$segmentId}) AS leads"),
                DB::raw("(SELECT COUNT(*) FROM bitrix_leads WHERE referrer_id = users.id AND deal_id > 0 AND segment = {$segmentId}) AS deals"),
            ])
            ->first();

        $referrer->deal_lead_conversion_ratio = $this->getRatio($referrer->deals, $referrer->leads);
        $referrer->applied_deal_conversion_ratio = $this->getRatio($referrer->applieds, $referrer->deals);

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
            ->select(['id',
                'referrer_id',
                'name',
                'last_name',
                'referrer_status',
                'deleted_at'])
            ->withCount('referrals as referrals_count')
            ->with(['daytypes' => function (HasMany $query) {
                $query->selectRaw("id, user_id, type, date,DATE_FORMAT(date, '%e') as day")
                    ->where('type', DayType::DAY_TYPES['TRAINEE'])
                    ->distinct();
            }])
            ->with(['timetracking' => function (HasMany $query) {
                $query->selectRaw("`enter`, `exit`, id, user_id, TIMESTAMPDIFF(minute, `enter`, `exit`) as work_total")
                    ->distinct()
                    ->havingRaw("work_total >= ?", [60 * 3]);
            }])
            ->with(['referrerSalaries' => function (HasMany $query) use ($referrer) {
                $query->where("referrer_id", $referrer->getKey());
                $query->select(["referrer_id", 'date', 'amount', 'comment', 'referral_id', 'type', 'id', 'is_paid']);
                $query->orderBy('date');
            }])
            ->with(['groups' => function (BelongsToMany $query) use ($referrer) {
                $query->wherePivot('status', 'active');
                $query->select(["id", 'name']);
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
                $firstWork = $this->salaryFilter->filter(PaidType::FIRST_WORK);
                $attestation = $this->salaryFilter->filter(PaidType::ATTESTATION);
                $referral->is_trainee = $referral->user_description?->is_trainee;
                $referral->datetypes = array_merge(
                    $this->traineesDaily($days, $training),
                    $this->attestation($attestation),
                    $this->employeeFirstWeek($firstWork),
                    $this->employeeWeekly($working)
                );

                if ($referral->referrals_count) {

                    if ($step <= 3) {
                        $referral->referrals = $this->referrals($referral, $step + 1);
                    }
                }

                return $referral;
            });
    }

    private function traineesDaily($days, $training): array
    {
        $types = [];
        for ($i = 1; $i <= $this->dateStart()->daysInMonth; $i++) {
            $types[$i] = null;
            $day = $this->getDay($days, $i);
            if ($day) {
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

    private function employeeWeekly(Collection $working): array
    {
        $weekTemplate = $this->createWeekTemplate();
        $salaryWeeks = [2, 3, 4, 6, 8, 12]; // Define the weeks at which salaries are given
        $salaryIndex = 0; // Index to track the current salary

        foreach ($working as $salary) {
            // Ensure there's another salary week to process
            if (isset($salaryWeeks[$salaryIndex])) {
                $weekLabel = $salaryWeeks[$salaryIndex] . '_week';
                $weekTemplate[$weekLabel] = $this->parseSalary($salary->toArray());
                $salaryIndex++;
            }
        }

        return $weekTemplate;
    }

    private function getDay(Collection $days, int $day): ?DayType
    {
        /** @var DayType $day */
        return $days
            ->where('day', $day)
            ->first();
    }

    private
    function countTrainingDays($training, DayType $day): ?array
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

    private
    function isSameDate(Carbon $first, Carbon $second): bool
    {
        return Carbon::parse($first)->format("Y-m-d") == $second->format("Y-m-d");
    }

    private function createWeekTemplate(): array
    {
        $types = [];

        $weeks = [2, 3, 4, 6, 8, 12];
        foreach ($weeks as $week) {
            $types[$week . '_week'] = null;
        }
        return $types;
    }

    private
    function parseSalary(?array $current): array
    {
        return [
            'paid' => (bool)($current['is_paid'] ?? null),
            'sum' => $current['amount'] ?? null,
            'comment' => $current['comment'] ?? null,
            'id' => $current['id'] ?? null,
            'date' => $current['date'] ?? null,
        ];
    }

    private function employeeFirstWeek(Collection $firstWork): array
    {
        $appliedSalary = current($firstWork->toArray());

        if (!$appliedSalary) {
            return [];
        }

        return [
            '1_week' => $this->parseSalary($appliedSalary)
        ];
    }
}