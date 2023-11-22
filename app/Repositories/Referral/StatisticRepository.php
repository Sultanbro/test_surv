<?php

namespace App\Repositories\Referral;

use App\Service\Referral\Core\LeadTemplate;
use App\Service\Referral\Core\PaidType;
use App\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;

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