<?php

namespace App\Repositories\Referral;

use App\Models\Referral\ReferralSalary;
use App\Service\Referral\Core\LeadTemplate;
use App\Service\Referral\Core\PaidType;
use App\Service\Referral\SalaryFilter;
use App\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Arr;
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

    private function tops(): array
    {
        return User::query()
            ->selectRaw('
        users.id as id, 
        users.name as name, 
        users.last_name as last_name, 
        users.referrer_status as referrer_status, 
        users.img_url as img_url, 
        COUNT(r.id) as applieds'
            )
            ->leftJoin('users as r', 'users.id', '=', 'r.referrer_id')
            ->leftJoin('user_descriptions as d', 'r.id', '=', 'd.user_id')
            ->where('d.is_trainee', 0)
            ->groupBy('users.id', 'users.name', 'users.last_name', 'users.referrer_status', 'users.img_url')
            ->orderBy('applieds', 'desc')
            ->take(5)
            ->get()
            ->toArray();
    }

    private function referrals(User $referrer, int $step = 1)
    {
        return $referrer->referrals()
            ->select([
                'id',
                'referrer_id',
                'name',
                'last_name',
                'referrer_status',
                'deleted_at'
            ])
            ->withCount('referrals as referrals_count')
            ->with(['referralSalaries' => function (HasMany $query) use ($referrer) {
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
            ->map(function ($referral) use ($referrer, $step) {

                $salaries = $referral->referrerSalaries;

                $referral->is_trainee = $referral->user_description?->is_trainee;

                $dateTypes = $this->table($salaries);

                $referral->datetypes = Arr::sort($dateTypes, 'date');

                if ($referral->referrals_count) {

                    if ($step <= 3) {
                        $referral->referrals = $this->referrals($referral, $step + 1);
                    }
                }

                return $referral;
            });
    }

    private function table(Collection $salaries): array
    {
        $this->salaryFilter->for($salaries);
        $training = $this->salaryFilter->filter(PaidType::TRAINEE);
        $working = $this->salaryFilter->filter(PaidType::WORK);
        $firstWork = $this->salaryFilter->filter(PaidType::FIRST_WORK)->first();
        $attestation = $this->salaryFilter->filter(PaidType::ATTESTATION)->first();

        return [
            ...$this->traineeDaysSalaries($training),
            ...$this->workingWeeksSalaries($working),
            '1_week' => $this->tryGetSalary($firstWork),
            'pass_certification' => $this->tryGetSalary($attestation)
        ];
    }

    private function traineeDaysSalaries(Collection $salaries): array
    {
        $days = [];
        foreach ($salaries as $day => $salary) {
            $days[$day] = $this->parseSalary($salary->toArray());
        }
        return $days;
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

    private function workingWeeksSalaries(Collection $salaries): array
    {
        $salaries = $salaries->sortBy('date');
        $weekTemplate = $this->createWeekTemplate();
        $salaryWeeks = [2, 3, 4, 6, 8, 12]; // Define the weeks at which salaries are given
        $salaryIndex = 0; // Index to track the current salary

        foreach ($salaries as $salary) {
            // Ensure there's another salary week to process
            if (isset($salaryWeeks[$salaryIndex])) {
                $weekTemplate[$salaryWeeks[$salaryIndex] . '_week'] = $this->parseSalary($salary->toArray());
                $salaryIndex++;
            }
        }

        return $weekTemplate;
    }

    private function createWeekTemplate(): array
    {
        $template = [];

        $weeks = [2, 3, 4, 6, 8, 12];

        foreach ($weeks as $week) {
            $template[$week . '_week'] = null;
        }

        return $template;
    }

    private function tryGetSalary(?ReferralSalary $salary): array
    {
        $appliedSalary = $salary?->toArray();

        if (!$appliedSalary) return [];

        return $this->parseSalary($appliedSalary);
    }
}