<?php

namespace App\Repositories\Referral;

use App\Service\Referral\Core\LeadTemplate;
use App\Service\Referral\Core\PaidType;
use App\Service\Referral\Scheduler;
use App\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

class StatisticRepository implements StatisticRepositoryInterface
{
    protected array $filter = [];

    public function __construct(
        private readonly Scheduler $scheduler
    )
    {
    }

    public function statistic(array $filter): array
    {
        $this->filter = $filter;
        $this->scheduler->setFilter($filter);
        $referrers = $this->referrers();
        return [
            'pivot' => $this->pivot($referrers),
            'referrers' => $this->schedule($referrers)
        ];
    }

    protected function pivot(Collection $referrers): array
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
            $deal_lead_conversion += $referer['deal_lead_conversion_ratio'];
            $countForDeals += (bool)$referer['leads'];
            $applied_deal_conversion += $referer['appiled_deal_conversion_ratio'];
            $countForApplied += (bool)$referer['deals'];
            $accepted += (int)$referer['applieds'];
            $paidTotal += (float)$referer['paid_total'];
            $paidTotalForMonth += (float)$referer['month_paid'];
            $earnedTotalForMonth += (float)$referer['month_earned'];
        }

        $deal_lead_conversion = $countForDeals ? $deal_lead_conversion / $countForDeals : 0;
        $applied_deal_conversion = $countForApplied ? $applied_deal_conversion / $countForApplied : 0;

        return [
            'employee_price' => $accepted ? $paidTotal / $accepted : 0,
            'deal_lead_conversion' => $deal_lead_conversion,
            'applied_deal_conversion' => $applied_deal_conversion,
            'earned' => $earnedTotalForMonth,
            'paid' => $paidTotalForMonth,
        ];
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

    protected function schedule(Collection $referrers): array
    {
        return $referrers
            ->each(function (User $user) {
                $user->deal_lead_conversion_ratio = $this->getRatio($user->deals, $user->leads);
                $user->appiled_deal_conversion_ratio = $this->getRatio($user->applieds, $user->deals);
                $user->referrers_earned = $user->from_referrals;
            })
            ->toArray();
    }

    protected function referrers(): Collection
    {
        $bindings = [
            $this->dateStart()->format("Y-m-d"),
            $this->dateEnd()->format("Y-m-d"),
        ];

        /** @var Collection<User> */
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
            ->selectRaw('(
            SELECT SUM(amount)
            FROM referral_salaries
            WHERE users.id = referral_salaries.referrer_id
                AND date BETWEEN ? AND ?
                AND type != ? 
        ) AS from_referrals', [
                ...$bindings,
                PaidType::WORK->name
            ])
            ->selectRaw('(
            SELECT SUM(amount)
            FROM referral_salaries
            WHERE users.id = referral_salaries.referrer_id
                AND is_paid != 1 
        ) AS paid_total')
            ->orderBy('leads', 'desc')
            ->get();
    }

    protected function getRatio($convertible, $to): float
    {
        if ($to) {
            return ceil((100 * $convertible) / $to);
        }
        return 0;
    }
}
