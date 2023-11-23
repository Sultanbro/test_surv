<?php

namespace App\Repositories\Referral;

use App\Service\Referral\Core\LeadTemplate;
use App\Service\Referral\Core\PaidType;
use App\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class StatisticRepository implements StatisticRepositoryInterface
{
    protected array $filter = [];

    public function statistic(array $filter): array
    {
        $this->filter = $filter;
        $referrers = $this->referrers();
        return [
            'pivot' => $this->pivot($referrers),
            'referrers' => $referrers
        ];
    }

    protected function pivot(array $referrers): array
    {
        $deal_lead_conversion = 0;
        $applied_deal_conversion = 0;
        $countForDeals = 0;
        $countForApplied = 0;
        $accepted = 0;
        $paidTotal = 0;
        $paidTotalForMonth = 0;
        $earnedTotalForMonth = 0;

        foreach ($referrers as $referer) {
            $accepted += $referer['applieds'];
            $paidTotal += $referer['absolute_paid'];
            $paidTotalForMonth += $referer['month_paid'];
            $earnedTotalForMonth += $referer['month_earned'];
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

        return [
            'employee_price' => $accepted ? $paidTotal / $accepted : 0,
            'deal_lead_conversion' => $deal_lead_conversion,
            'applied_deal_conversion' => $applied_deal_conversion,
            'earned' => $earnedTotalForMonth,
            'paid' => $paidTotalForMonth,
        ];
    }

    protected function referrers(): array
    {
        return User::query()
            ->WhereHas('referralLeads')
            ->with(['user_description' => fn($query) => $query->select(['id', 'user_id', 'is_trainee'])])
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
            ->orderBy('leads', 'desc')
            ->get()
            ->each(function (User $user) {
                $user->deal_lead_conversion_ratio = $this->getRatio($user->deals, $user->leads);
                $user->appiled_deal_conversion_ratio = $this->getRatio($user->applieds, $user->deals);
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

    protected function getRatio($convertible, $to): float
    {
        if ($to) {
            return ceil((100 * $convertible) / $to);
        }
        return 0;
    }
}